<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use App\Models\Notification;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\ViewingRequest;
use App\Services\GeminiAiService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WebLandlordController extends Controller
{
    public function __construct(protected GeminiAiService $aiService)
    {
    }

    public function dashboard()
    {
        $user = auth()->user();
        $properties = Property::where('landlord_id', $user->id)
            ->with(['images', 'amenities', 'viewingRequests'])
            ->latest()
            ->get();

        $viewingRequests = ViewingRequest::where('landlord_id', $user->id)
            ->with(['property', 'user'])
            ->latest()
            ->take(5)
            ->get();

        $stats = [
            'total_properties' => $properties->count(),
            'available_count' => $properties->where('status', 'available')->count(),
            'rented_count' => $properties->where('status', 'rented')->count(),
            'total_views' => $properties->sum('views_count'),
            'pending_viewings' => ViewingRequest::where('landlord_id', $user->id)->where('status', 'pending')->count(),
        ];

        return view('landlord.dashboard', compact('properties', 'viewingRequests', 'stats'));
    }

    public function createProperty()
    {
        $amenities = Amenity::all();
        $districts = [
            'Sala Kamreuk',
            'Wat Bo',
            'Svay Dangkum',
            'Slor Kram',
            'Kork Chak',
            'Chreav',
            'Sambour',
        ];

        return view('landlord.properties.create', compact('amenities', 'districts'));
    }

    public function storeProperty(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'property_type' => 'required|string',
            'price_per_month' => 'required|numeric|min:1',
            'deposit_amount' => 'nullable|numeric|min:0',
            'bedrooms' => 'required|integer|min:1',
            'bathrooms' => 'required|integer|min:1',
            'size_sqm' => 'nullable|numeric|min:1',
            'city' => 'required|string',
            'khan_district' => 'required|string',
            'street_address' => 'required|string',
            'electricity_cost_per_kwh' => 'nullable|numeric',
            'water_cost_per_m3' => 'nullable|numeric',
            'furnished_status' => 'required|string',
            'amenities' => 'nullable|array',
            'image_urls' => 'nullable|string',
        ]);

        $completeness = $this->aiService->checkListingCompleteness($request->all());

        $data = $request->except(['amenities', 'image_urls']);
        $data['landlord_id'] = auth()->id();
        $data['slug'] = Str::slug($request->title) . '-' . Str::random(6);
        $data['ai_completeness_score'] = $completeness['completeness_score'];
        $data['status'] = 'available';

        $property = Property::create($data);

        // Attach amenities
        if ($request->has('amenities')) {
            $property->amenities()->sync($request->amenities);
        }

        // Add images from URLs
        if ($request->filled('image_urls')) {
            $urls = array_filter(array_map('trim', explode("\n", $request->image_urls)));
            foreach ($urls as $idx => $url) {
                if (filter_var($url, FILTER_VALIDATE_URL)) {
                    PropertyImage::create([
                        'property_id' => $property->id,
                        'image_path' => $url,
                        'is_primary' => $idx === 0,
                        'image_hash' => md5($url),
                        'sort_order' => $idx,
                    ]);
                }
            }
        } else {
            // Default high-res room image
            PropertyImage::create([
                'property_id' => $property->id,
                'image_path' => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80',
                'is_primary' => true,
                'image_hash' => md5('default'),
            ]);
        }

        return redirect()->route('landlord.dashboard')
            ->with('success', 'Your property was listed successfully with an AI Completeness score of ' . $property->ai_completeness_score . '%!');
    }

    public function viewings()
    {
        $viewings = ViewingRequest::where('landlord_id', auth()->id())
            ->with(['property', 'user'])
            ->latest()
            ->paginate(15);

        return view('landlord.viewings', compact('viewings'));
    }

    public function updateViewingStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:confirmed,completed,cancelled,rejected',
            'landlord_response' => 'nullable|string|max:500',
        ]);

        $viewing = ViewingRequest::where('id', $id)
            ->where('landlord_id', auth()->id())
            ->firstOrFail();

        $viewing->update([
            'status' => $request->status,
            'landlord_response' => $request->landlord_response,
        ]);

        Notification::create([
            'user_id' => $viewing->user_id,
            'title' => 'Viewing Request ' . ucfirst($request->status),
            'message' => "Your viewing appointment for '{$viewing->property->title}' has been updated to: {$request->status}.",
            'type' => 'booking_status',
            'link' => route('renter.dashboard'),
        ]);

        return back()->with('success', "Viewing request marked as {$request->status}.");
    }

    public function deleteProperty(int $id)
    {
        $property = Property::where('id', $id)
            ->where('landlord_id', auth()->id())
            ->firstOrFail();

        $property->delete();

        return back()->with('success', 'Property listing removed.');
    }
}
