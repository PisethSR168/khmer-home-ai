<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use App\Models\Property;
use App\Services\GeminiAiService;
use Illuminate\Http\Request;

class WebPropertyController extends Controller
{
    public function __construct(protected GeminiAiService $aiService)
    {
    }

    public function index(Request $request)
    {
        $query = Property::query()->available()->with(['images', 'amenities', 'landlord.landlordProfile']);

        // District filter
        if ($request->filled('district')) {
            $query->where('khan_district', $request->district);
        }

        // Property type
        if ($request->filled('property_type')) {
            $query->where('property_type', $request->property_type);
        }

        // Price range
        if ($request->filled('min_price')) {
            $query->where('price_per_month', '>=', (float) $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price_per_month', '<=', (float) $request->max_price);
        }

        // Bedrooms
        if ($request->filled('bedrooms')) {
            $query->where('bedrooms', '>=', (int) $request->bedrooms);
        }

        // Furnished status
        if ($request->filled('furnished_status')) {
            $query->where('furnished_status', $request->furnished_status);
        }

        // Pet friendly
        if ($request->boolean('pet_friendly')) {
            $query->where('pet_friendly', true);
        }

        // Wi-Fi included
        if ($request->boolean('wifi_included')) {
            $query->where('wifi_included', true);
        }

        // Search text
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%")
                  ->orWhere('khan_district', 'like', "%{$s}%")
                  ->orWhere('street_address', 'like', "%{$s}%");
            });
        }

        // Amenities
        if ($request->filled('amenities') && is_array($request->amenities)) {
            foreach ($request->amenities as $slug) {
                $query->whereHas('amenities', function ($q) use ($slug) {
                    $q->where('slug', $slug);
                });
            }
        }

        // Sorting
        $sort = $request->get('sort', 'latest');
        match ($sort) {
            'price_asc' => $query->orderBy('price_per_month', 'asc'),
            'price_desc' => $query->orderBy('price_per_month', 'desc'),
            'popular' => $query->orderBy('views_count', 'desc'),
            default => $query->orderBy('is_featured', 'desc')->latest(),
        };

        $properties = $query->paginate(9)->withQueryString();
        $amenities = Amenity::all();

        return view('properties.index', compact('properties', 'amenities'));
    }

    public function show(string $slug)
    {
        $property = Property::where('slug', $slug)
            ->with(['images', 'amenities', 'landlord.landlordProfile', 'reviews.user'])
            ->firstOrFail();

        $property->increment('views_count');

        // AI Recommendations for similar properties
        $recommendations = $this->aiService->smartRecommendations(
            auth()->id(),
            $property->id
        )['recommendations'] ?? collect();

        return view('properties.show', compact('property', 'recommendations'));
    }
}
