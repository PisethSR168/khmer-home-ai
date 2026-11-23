<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Amenity;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Services\GeminiAiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PropertyController extends BaseApiController
{
    public function __construct(protected GeminiAiService $aiService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $query = Property::query()->available()->with(['images', 'amenities', 'landlord.landlordProfile']);

        // District filter
        if ($request->filled('district')) {
            $query->where('khan_district', 'like', '%' . $request->district . '%');
        }

        // City filter
        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        // Price range
        if ($request->filled('min_price')) {
            $query->where('price_per_month', '>=', (float) $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price_per_month', '<=', (float) $request->max_price);
        }

        // Property type
        if ($request->filled('property_type')) {
            $query->where('property_type', $request->property_type);
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

        // Keyword search (title, address, description)
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%")
                  ->orWhere('street_address', 'like', "%{$s}%")
                  ->orWhere('khan_district', 'like', "%{$s}%")
                  ->orWhere('sangkat_commune', 'like', "%{$s}%");
            });
        }

        // Amenities filtering
        if ($request->filled('amenities')) {
            $amenitySlugs = is_array($request->amenities) ? $request->amenities : explode(',', $request->amenities);
            foreach ($amenitySlugs as $slug) {
                $slug = trim($slug);
                if (!empty($slug)) {
                    $query->whereHas('amenities', function ($q) use ($slug) {
                        $q->where('slug', $slug);
                    });
                }
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

        $perPage = min((int) $request->get('per_page', 12), 50);
        $properties = $query->paginate($perPage);

        return $this->sendResponse($properties);
    }

    public function show(string $idOrSlug): JsonResponse
    {
        $property = Property::where('id', $idOrSlug)
            ->orWhere('slug', $idOrSlug)
            ->with(['images', 'amenities', 'landlord.landlordProfile', 'reviews.user'])
            ->first();

        if (!$property) {
            return $this->sendError('Property not found', [], 404);
        }

        $property->increment('views_count');

        return $this->sendResponse($property);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->isLandlord() && !$user->isAdmin()) {
            return $this->sendError('Unauthorized. Only registered landlords can post properties.', [], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'property_type' => 'required|string|in:room,studio,apartment,condo,house',
            'price_per_month' => 'required|numeric|min:1',
            'deposit_amount' => 'nullable|numeric|min:0',
            'bedrooms' => 'required|integer|min:1',
            'bathrooms' => 'required|integer|min:1',
            'size_sqm' => 'nullable|numeric|min:1',
            'floor_number' => 'nullable|integer',
            'city' => 'required|string',
            'khan_district' => 'required|string',
            'sangkat_commune' => 'nullable|string',
            'street_address' => 'required|string',
            'electricity_cost_per_kwh' => 'nullable|numeric',
            'water_cost_per_m3' => 'nullable|numeric',
            'wifi_included' => 'nullable|boolean',
            'parking_available' => 'nullable|boolean',
            'pet_friendly' => 'nullable|boolean',
            'furnished_status' => 'required|string|in:unfurnished,semi_furnished,fully_furnished',
            'amenities' => 'nullable|array',
            'images' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors()->toArray(), 422);
        }

        // Run AI listing completeness check
        $completeness = $this->aiService->checkListingCompleteness($request->all());

        $data = $request->except(['amenities', 'images']);
        $data['landlord_id'] = $user->id;
        $data['slug'] = Str::slug($request->title) . '-' . Str::random(6);
        $data['ai_completeness_score'] = $completeness['completeness_score'];
        $data['status'] = 'available';

        $property = Property::create($data);

        // Sync amenities
        if ($request->has('amenities')) {
            $amenityIds = Amenity::whereIn('id', $request->amenities)
                ->orWhereIn('slug', $request->amenities)
                ->pluck('id');
            $property->amenities()->sync($amenityIds);
        }

        // Add images
        if ($request->has('images') && is_array($request->images)) {
            foreach ($request->images as $index => $img) {
                $path = is_array($img) ? ($img['url'] ?? '') : $img;
                if (!empty($path)) {
                    PropertyImage::create([
                        'property_id' => $property->id,
                        'image_path' => $path,
                        'is_primary' => $index === 0,
                        'caption' => is_array($img) ? ($img['caption'] ?? null) : null,
                        'image_hash' => md5($path),
                        'sort_order' => $index,
                    ]);
                }
            }
        }

        return $this->sendResponse(
            $property->load(['images', 'amenities']),
            'Property listing published successfully',
            201
        );
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $property = Property::find($id);

        if (!$property) {
            return $this->sendError('Property not found', [], 404);
        }

        if ($property->landlord_id !== $user->id && !$user->isAdmin()) {
            return $this->sendError('Unauthorized action', [], 403);
        }

        $property->update($request->except(['landlord_id', 'slug', 'amenities']));

        if ($request->has('amenities')) {
            $amenityIds = Amenity::whereIn('id', $request->amenities)
                ->orWhereIn('slug', $request->amenities)
                ->pluck('id');
            $property->amenities()->sync($amenityIds);
        }

        return $this->sendResponse(
            $property->fresh(['images', 'amenities', 'landlord']),
            'Property updated successfully'
        );
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $property = Property::find($id);

        if (!$property) {
            return $this->sendError('Property not found', [], 404);
        }

        if ($property->landlord_id !== $user->id && !$user->isAdmin()) {
            return $this->sendError('Unauthorized action', [], 403);
        }

        $property->delete();

        return $this->sendResponse(null, 'Property listing deleted successfully');
    }

    public function amenities(): JsonResponse
    {
        $amenities = Amenity::all()->groupBy('category');
        return $this->sendResponse($amenities);
    }
}
