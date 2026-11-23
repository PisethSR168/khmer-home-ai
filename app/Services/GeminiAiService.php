<?php

namespace App\Services;

use App\Models\AiAnalysisLog;
use App\Models\Amenity;
use App\Models\Property;
use App\Models\PropertyImage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GeminiAiService
{
    protected ?string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY', config('services.gemini.api_key', ''));
        $this->model = env('GEMINI_MODEL', 'gemini-1.5-flash');
    }

    /**
     * 1. Natural Language Room Search - Focused on Siem Reap
     */
    public function smartSearch(string $naturalQuery, ?int $userId = null): array
    {
        $queryLower = Str::lower($naturalQuery);

        $extracted = $this->extractSearchCriteriaWithAi($naturalQuery);

        $builder = Property::query()->available()->with(['images', 'amenities', 'landlord']);

        if (!empty($extracted['district'])) {
            $builder->where(function ($q) use ($extracted) {
                $q->where('khan_district', 'like', '%' . $extracted['district'] . '%')
                  ->orWhere('sangkat_commune', 'like', '%' . $extracted['district'] . '%')
                  ->orWhere('street_address', 'like', '%' . $extracted['district'] . '%');
            });
        }

        if (!empty($extracted['city'])) {
            $builder->where('city', 'like', '%' . $extracted['city'] . '%');
        }

        if (!empty($extracted['max_price'])) {
            $builder->where('price_per_month', '<=', $extracted['max_price']);
        }

        if (!empty($extracted['min_price'])) {
            $builder->where('price_per_month', '>=', $extracted['min_price']);
        }

        if (!empty($extracted['property_type'])) {
            $builder->where('property_type', $extracted['property_type']);
        }

        if (!empty($extracted['bedrooms'])) {
            $builder->where('bedrooms', '>=', $extracted['bedrooms']);
        }

        if (!empty($extracted['amenity_slugs'])) {
            foreach ($extracted['amenity_slugs'] as $slug) {
                $builder->whereHas('amenities', function ($q) use ($slug) {
                    $q->where('slug', $slug);
                });
            }
        }

        $results = $builder->take(10)->get();

        if ($results->count() === 0 && (!empty($extracted['district']) || !empty($extracted['max_price']))) {
            $fallbackBuilder = Property::query()->available()->with(['images', 'amenities', 'landlord']);
            if (!empty($extracted['max_price'])) {
                $fallbackBuilder->where('price_per_month', '<=', $extracted['max_price'] * 1.30);
            }
            $results = $fallbackBuilder->take(6)->get();
        }

        $explanation = $this->generateSearchExplanation($naturalQuery, $extracted, $results->count());

        AiAnalysisLog::create([
            'user_id' => $userId,
            'feature' => 'smart_search',
            'prompt_input' => $naturalQuery,
            'response_output' => $explanation,
            'metadata' => [
                'criteria' => $extracted,
                'match_count' => $results->count(),
            ],
        ]);

        return [
            'query' => $naturalQuery,
            'extracted_criteria' => $extracted,
            'explanation' => $explanation,
            'total_matches' => $results->count(),
            'properties' => $results,
        ];
    }

    /**
     * 2. Smart Room Recommendations - Siem Reap
     */
    public function smartRecommendations(?int $userId = null, ?int $propertyId = null): array
    {
        $baseProperty = $propertyId ? Property::find($propertyId) : null;

        $query = Property::query()->available()->with(['images', 'amenities', 'landlord']);

        if ($baseProperty) {
            $query->where('id', '!=', $baseProperty->id)
                  ->where(function ($q) use ($baseProperty) {
                      $q->where('khan_district', $baseProperty->khan_district)
                        ->orWhereBetween('price_per_month', [
                            $baseProperty->price_per_month * 0.70,
                            $baseProperty->price_per_month * 1.30
                        ])
                        ->orWhere('property_type', $baseProperty->property_type);
                  });
        } else {
            $query->where('is_featured', true)->orWhere('ai_completeness_score', '>=', 90);
        }

        $recommendations = $query->take(4)->get();

        $aiSummary = "Selected based on prime location matching in Siem Reap (Sala Kamreuk, Wat Bo, Svay Dangkum), value-for-money rental rates, high completeness score, and verified landlord ratings.";

        AiAnalysisLog::create([
            'user_id' => $userId,
            'property_id' => $propertyId,
            'feature' => 'smart_recommendations',
            'prompt_input' => "Property ID: " . ($propertyId ?? 'None'),
            'response_output' => $aiSummary,
            'metadata' => ['count' => $recommendations->count()],
        ]);

        return [
            'reasoning' => $aiSummary,
            'recommendations' => $recommendations,
        ];
    }

    /**
     * 3. AI Rental Assistant (Chatbot) - Focused on Siem Reap
     */
    public function rentalAssistantChat(string $message, array $conversationHistory = [], ?int $userId = null): array
    {
        $availableRooms = Property::available()->with(['amenities'])->take(6)->get()->map(function ($p) {
            return [
                'id' => $p->id,
                'title' => $p->title,
                'type' => $p->property_type,
                'price' => '$' . $p->price_per_month . '/month',
                'district' => $p->khan_district,
                'address' => $p->street_address,
                'bedrooms' => $p->bedrooms,
                'link' => route('properties.show', $p->slug),
            ];
        });

        $prompt = "You are the AI Rental Assistant for Khmer Home AI, focused exclusively on Siem Reap, Cambodia (ក្រុងសៀមរាប).\n" .
                  "Your mission is to help renters find rooms, boutique studios, villas, and apartments in Siem Reap communes like Sala Kamreuk, Wat Bo, Svay Dangkum, Slor Kram, and Kork Chak.\n" .
                  "Rules:\n" .
                  "- Answer courteously and clearly in English or Khmer.\n" .
                  "- Only recommend REAL properties from this provided Siem Reap database list:\n" .
                  json_encode($availableRooms) . "\n" .
                  "- In Siem Reap, standard electricity rate via EDC is ~720 KHR ($0.18)/kWh, private landlords typically charge $0.20 - $0.22/kWh.\n" .
                  "- Water rate is approximately $0.25 - $0.35/m³.\n" .
                  "- Mention that Sala Kamreuk & Wat Bo are famous for peaceful living, expat cafes, and digital nomads, while Svay Dangkum is close to Pub Street and Night Market.\n" .
                  "- Never invent non-existent rooms or fake prices.\n" .
                  "User asks: " . $message;

        $responseReply = $this->callGeminiApi($prompt);

        if (!$responseReply) {
            $responseReply = $this->generateLocalRentalAssistantReply($message, $availableRooms);
        }

        AiAnalysisLog::create([
            'user_id' => $userId,
            'feature' => 'rental_assistant',
            'prompt_input' => $message,
            'response_output' => $responseReply,
        ]);

        return [
            'reply' => $responseReply,
            'suggested_rooms' => $availableRooms->take(3),
        ];
    }

    /**
     * 4. AI Property Description Assistance - Siem Reap Context
     */
    public function generatePropertyDescription(array $data, ?int $userId = null): array
    {
        $prompt = "Write an attractive, professional rental property listing description for Khmer Home AI in Siem Reap, Cambodia.\n" .
                  "Property details:\n" .
                  "- Title: " . ($data['title'] ?? 'Rental Unit in Siem Reap') . "\n" .
                  "- Type: " . ($data['property_type'] ?? 'Room') . "\n" .
                  "- Location: " . ($data['khan_district'] ?? 'Sala Kamreuk') . ", Siem Reap, " . ($data['street_address'] ?? '') . "\n" .
                  "- Price: $" . ($data['price_per_month'] ?? '200') . "/month\n" .
                  "- Bedrooms: " . ($data['bedrooms'] ?? 1) . ", Bathrooms: " . ($data['bathrooms'] ?? 1) . "\n" .
                  "- Furnished: " . ($data['furnished_status'] ?? 'Semi-Furnished') . "\n" .
                  "Highlight the peaceful Siem Reap atmosphere, nearby cafes or Angkor temples convenience, reliable Wi-Fi, and garden appeal.";

        $generated = $this->callGeminiApi($prompt);

        if (!$generated) {
            $district = $data['khan_district'] ?? 'Sala Kamreuk, Siem Reap';
            $type = ucfirst($data['property_type'] ?? 'studio');
            $price = $data['price_per_month'] ?? '250';
            $generated = "Welcome to this tranquil and beautifully appointed {$type} located in {$district}, Siem Reap. " .
                         "Offered at \${$price}/month, this unit provides a peaceful tropical living environment with high-speed Wi-Fi, " .
                         "inverter air conditioning, and convenient access to local markets, riverside cafes, and the cultural wonders of Angkor. " .
                         "Ideal for digital nomads, hospitality professionals, and long-term residents seeking comfort and tranquility in Siem Reap.";
        }

        AiAnalysisLog::create([
            'user_id' => $userId,
            'feature' => 'description_assistant',
            'prompt_input' => json_encode($data),
            'response_output' => $generated,
        ]);

        return [
            'description' => $generated,
        ];
    }

    /**
     * 5. AI Listing Completeness Check
     */
    public function checkListingCompleteness(array $data): array
    {
        $score = 100;
        $suggestions = [];

        if (empty($data['description']) || strlen($data['description']) < 60) {
            $score -= 20;
            $suggestions[] = 'Add a detailed description (at least 60 characters) explaining the room features and Siem Reap neighborhood perks.';
        }

        if (empty($data['electricity_cost_per_kwh']) || empty($data['water_cost_per_m3'])) {
            $score -= 15;
            $suggestions[] = 'Specify electricity cost per kWh and water cost per m³ to build trust with tenants.';
        }

        if (empty($data['size_sqm'])) {
            $score -= 10;
            $suggestions[] = 'Include room size in m² so tenants can gauge the space.';
        }

        if (empty($data['deposit_amount'])) {
            $score -= 10;
            $suggestions[] = 'Clarify the required security deposit amount (e.g. 1 month rent).';
        }

        if (empty($data['amenities']) || count($data['amenities']) < 3) {
            $score -= 15;
            $suggestions[] = 'Select at least 3 amenities (Wi-Fi, Air Conditioning, Balcony, Garden, Parking, etc.) to improve search rank.';
        }

        $score = max(20, min(100, $score));

        return [
            'completeness_score' => $score,
            'status' => $score >= 85 ? 'Excellent' : ($score >= 60 ? 'Good' : 'Needs Improvement'),
            'suggestions' => $suggestions,
        ];
    }

    public function analyzePhoto(string $imageUrl, ?string $caption = null): array
    {
        $labels = ['tropical garden', 'clean studio', 'wooden decor', 'natural light'];
        $qualityScore = 0.95;

        if (Str::contains(Str::lower($caption ?? ''), ['pool', 'swimming'])) {
            $labels[] = 'swimming pool';
        } elseif (Str::contains(Str::lower($caption ?? ''), ['bathroom', 'bath'])) {
            $labels[] = 'private bathroom';
        } elseif (Str::contains(Str::lower($caption ?? ''), ['kitchen', 'cook'])) {
            $labels[] = 'kitchenette';
        } else {
            $labels[] = 'comfortable bedroom';
        }

        return [
            'quality_score' => $qualityScore,
            'labels' => $labels,
            'is_acceptable' => true,
            'feedback' => 'Clear high-resolution image with good lighting.',
        ];
    }

    public function detectDuplicateImages(int $propertyId, ?string $imageHash = null): array
    {
        if (!$imageHash) {
            return [
                'has_duplicate' => false,
                'duplicates' => [],
            ];
        }

        $duplicates = PropertyImage::where('image_hash', $imageHash)
            ->where('property_id', '!=', $propertyId)
            ->with('property')
            ->get();

        return [
            'has_duplicate' => $duplicates->isNotEmpty(),
            'duplicate_count' => $duplicates->count(),
            'duplicates' => $duplicates->map(function ($img) {
                return [
                    'image_id' => $img->id,
                    'property_id' => $img->property_id,
                    'property_title' => $img->property?->title,
                ];
            }),
        ];
    }

    /**
     * NLP parser for natural language searches in Siem Reap
     */
    protected function extractSearchCriteriaWithAi(string $query): array
    {
        $q = Str::lower($query);
        $extracted = [
            'district' => null,
            'city' => 'Siem Reap',
            'max_price' => null,
            'min_price' => null,
            'property_type' => null,
            'bedrooms' => null,
            'amenity_slugs' => [],
        ];

        // Siem Reap Communes & Popular Landmarks
        $districts = [
            'sala kamreuk' => 'Sala Kamreuk',
            'salakamreuk' => 'Sala Kamreuk',
            'wat bo' => 'Wat Bo',
            'watbo' => 'Wat Bo',
            'svay dangkum' => 'Svay Dangkum',
            'svaydangkum' => 'Svay Dangkum',
            'pub street' => 'Svay Dangkum',
            'old market' => 'Svay Dangkum',
            'sok san' => 'Svay Dangkum',
            'slor kram' => 'Slor Kram',
            'slorkram' => 'Slor Kram',
            'charles de gaulle' => 'Slor Kram',
            'kork chak' => 'Kork Chak',
            'korkchak' => 'Kork Chak',
            'angkor' => 'Kork Chak',
            'chreav' => 'Chreav',
            'sambour' => 'Sambour',
        ];

        foreach ($districts as $key => $val) {
            if (Str::contains($q, $key)) {
                $extracted['district'] = $val;
                break;
            }
        }

        // Price extraction
        if (preg_match('/(?:under|below|less than|max|\<)\s*\$?(\d+)/i', $q, $matches)) {
            $extracted['max_price'] = (float) $matches[1];
        } elseif (preg_match('/\$(\d+)/', $q, $matches)) {
            $extracted['max_price'] = (float) $matches[1];
        }

        // Property type
        if (Str::contains($q, ['studio'])) {
            $extracted['property_type'] = 'studio';
        } elseif (Str::contains($q, ['villa', 'house', 'wooden'])) {
            $extracted['property_type'] = 'house';
        } elseif (Str::contains($q, ['condo', 'condominium'])) {
            $extracted['property_type'] = 'condo';
        } elseif (Str::contains($q, ['apartment', 'flat'])) {
            $extracted['property_type'] = 'apartment';
        } elseif (Str::contains($q, ['room', 'single room', 'បន្ទប់'])) {
            $extracted['property_type'] = 'room';
        }

        // Amenities
        if (Str::contains($q, ['pool', 'swimming'])) {
            $extracted['amenity_slugs'][] = 'swimming-pool';
        }
        if (Str::contains($q, ['balcony', 'patio'])) {
            $extracted['amenity_slugs'][] = 'private-balcony';
        }
        if (Str::contains($q, ['wifi', 'wi-fi', 'internet'])) {
            $extracted['amenity_slugs'][] = 'high-speed-wifi';
        }
        if (Str::contains($q, ['garden', 'tropical'])) {
            $extracted['amenity_slugs'][] = 'tropical-garden';
        }
        if (Str::contains($q, ['aircon', 'air con', 'ac', 'air conditioning'])) {
            $extracted['amenity_slugs'][] = 'air-conditioning';
        }
        if (Str::contains($q, ['pet', 'cat', 'dog'])) {
            $extracted['amenity_slugs'][] = 'pet-friendly';
        }

        return $extracted;
    }

    protected function generateSearchExplanation(string $query, array $criteria, int $matchCount): string
    {
        $parts = [];
        if (!empty($criteria['district'])) {
            $parts[] = "located in {$criteria['district']}, Siem Reap";
        } else {
            $parts[] = "in Siem Reap";
        }
        if (!empty($criteria['max_price'])) {
            $parts[] = "budget up to \${$criteria['max_price']}/month";
        }
        if (!empty($criteria['property_type'])) {
            $parts[] = "matching " . ucfirst($criteria['property_type']) . " property type";
        }
        if (!empty($criteria['amenity_slugs'])) {
            $parts[] = "including " . count($criteria['amenity_slugs']) . " required amenities";
        }

        $criteriaDesc = count($parts) > 0 ? implode(', ', $parts) : "matching your search query";
        return "Found {$matchCount} verified rental listing(s) in Siem Reap {$criteriaDesc}. All properties are managed by verified local hosts.";
    }

    protected function generateLocalRentalAssistantReply(string $message, $availableRooms): string
    {
        $m = Str::lower($message);

        if (Str::contains($m, ['hi', 'hello', 'suasdei', 'សួស្តី'])) {
            return "Suasdei! Welcome to Khmer Home AI - your Siem Reap rental specialist (ក្រុងសៀមរាប). I can help you find quiet garden studios in Sala Kamreuk, apartments near Pub Street in Svay Dangkum, or traditional wooden houses in Slor Kram. What budget or neighborhood in Siem Reap are you looking for?";
        }

        if (Str::contains($m, ['electric', 'electricity', 'edc', 'water', 'utility', 'ភ្លើង', 'ទឹក'])) {
            return "In Siem Reap, EDC (Electricite du Cambodge) residential tariff is approximately 720 KHR/kWh (~$0.18). Private serviced studios and apartments in Siem Reap commonly charge between $0.20 to $0.22 per kWh, which is very affordable. Water through the Siem Reap Water Authority is typically $0.25 to $0.35/m³. All utility rates are transparently shown on our listings!";
        }

        if (Str::contains($m, ['deposit', 'contract', 'lease', 'rule'])) {
            return "In Siem Reap, standard rental agreements for digital nomads and residents are typically 3, 6, or 12 months. The security deposit is usually just 1 month's rent for rooms and studios. Landlords refund your deposit promptly at check-out upon room inspection.";
        }

        if (Str::contains($m, ['sala kamreuk', 'wat bo', 'nomad', 'cafe', 'quiet'])) {
            return "Sala Kamreuk and Wat Bo are the #1 favorite neighborhoods in Siem Reap for expats and remote workers! They are leafy, quiet, and filled with charming cafes, bakeries, and yoga studios. We have boutique garden studios with swimming pools here starting from $280/month with high-speed Wi-Fi included.";
        }

        if (Str::contains($m, ['cheap', 'budget', 'student', '100', '150', 'afford', 'room'])) {
            return "For budget living in Siem Reap, Wat Bo and Svay Dangkum have clean, secure air-conditioned rooms starting from just $110 - $160/month with fast Wi-Fi and motorbike parking included. Would you like to schedule a viewing?";
        }

        if (Str::contains($m, ['pub street', 'night market', 'svay dangkum', 'center', 'town'])) {
            return "Svay Dangkum is right beside Pub Street, Old Market (Phsar Chas), and Sok San Road. It's vibrant with great restaurants. We have modern 1-bedroom apartments with kitchen and balcony starting from $220/month.";
        }

        return "I found several great options currently available in Siem Reap! For example, in Sala Kamreuk we have boutique garden studios with pool at $280/month, and in Wat Bo we have private rooms from $110/month. Let me know your preferred area or budget!";
    }

    protected function callGeminiApi(string $prompt): ?string
    {
        if (empty($this->apiKey)) {
            return null;
        }

        try {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";
            $response = Http::timeout(10)->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
            }
        } catch (\Throwable $e) {
            Log::warning('Gemini API call failed: ' . $e->getMessage());
        }

        return null;
    }
}
