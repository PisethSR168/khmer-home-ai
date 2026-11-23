<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Notification;
use App\Models\Property;
use App\Models\ViewingRequest;
use Illuminate\Http\Request;

class WebRenterController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();

        $favorites = Property::whereHas('favorites', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->with(['images', 'amenities', 'landlord'])->get();

        $viewingRequests = ViewingRequest::where('user_id', $user->id)
            ->with(['property.images', 'landlord'])
            ->latest()
            ->get();

        $notifications = $user->notifications()->take(10)->get();

        return view('renter.dashboard', compact('favorites', 'viewingRequests', 'notifications'));
    }

    public function toggleFavorite(int $propertyId)
    {
        $user = auth()->user();
        $property = Property::findOrFail($propertyId);

        $fav = Favorite::where('user_id', $user->id)->where('property_id', $propertyId)->first();

        if ($fav) {
            $fav->delete();
            $msg = 'Removed from favorites';
        } else {
            Favorite::create([
                'user_id' => $user->id,
                'property_id' => $propertyId,
            ]);
            $msg = 'Saved to favorites!';
        }

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'is_favorited' => !$fav, 'message' => $msg]);
        }

        return back()->with('success', $msg);
    }

    public function requestViewing(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'preferred_date' => 'required|date|after_or_equal:today',
            'preferred_time' => 'required|string',
            'contact_phone' => 'required|string|max:30',
            'renter_note' => 'nullable|string|max:500',
        ]);

        $property = Property::findOrFail($request->property_id);
        $user = auth()->user();

        $viewing = ViewingRequest::create([
            'user_id' => $user->id,
            'property_id' => $property->id,
            'landlord_id' => $property->landlord_id,
            'preferred_date' => $request->preferred_date,
            'preferred_time' => $request->preferred_time,
            'contact_phone' => $request->contact_phone,
            'renter_note' => $request->renter_note,
            'status' => 'pending',
        ]);

        Notification::create([
            'user_id' => $property->landlord_id,
            'title' => 'New Room Viewing Request',
            'message' => "{$user->name} requested a viewing for '{$property->title}' on {$request->preferred_date} at {$request->preferred_time}.",
            'type' => 'viewing_request',
            'link' => route('landlord.viewings'),
            'data' => ['viewing_id' => $viewing->id],
        ]);

        return back()->with('success', 'Your viewing request has been sent to the landlord! They will confirm your appointment shortly.');
    }
}
