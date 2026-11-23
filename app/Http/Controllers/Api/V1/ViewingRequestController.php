<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Notification;
use App\Models\Property;
use App\Models\ViewingRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ViewingRequestController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->isLandlord()) {
            $requests = ViewingRequest::where('landlord_id', $user->id)
                ->with(['property.images', 'user'])
                ->latest()
                ->get();
        } else {
            $requests = ViewingRequest::where('user_id', $user->id)
                ->with(['property.images', 'landlord.landlordProfile'])
                ->latest()
                ->get();
        }

        return $this->sendResponse($requests);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'property_id' => 'required|exists:properties,id',
            'preferred_date' => 'required|date|after_or_equal:today',
            'preferred_time' => 'required|string',
            'contact_phone' => 'required|string|max:30',
            'renter_note' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors()->toArray(), 422);
        }

        $property = Property::findOrFail($request->property_id);
        $user = $request->user();

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

        // Create notification for landlord
        Notification::create([
            'user_id' => $property->landlord_id,
            'title' => 'New Room Viewing Request',
            'message' => "{$user->name} requested a viewing for '{$property->title}' on {$request->preferred_date} at {$request->preferred_time}.",
            'type' => 'viewing_request',
            'link' => '/landlord/viewings',
            'data' => [
                'viewing_id' => $viewing->id,
                'property_id' => $property->id,
            ],
        ]);

        return $this->sendResponse(
            $viewing->load(['property', 'landlord']),
            'Viewing request submitted successfully',
            201
        );
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:confirmed,completed,cancelled,rejected',
            'landlord_response' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors()->toArray(), 422);
        }

        $user = $request->user();
        $viewing = ViewingRequest::with('property')->findOrFail($id);

        if ($viewing->landlord_id !== $user->id && !$user->isAdmin()) {
            return $this->sendError('Unauthorized', [], 403);
        }

        $viewing->update([
            'status' => $request->status,
            'landlord_response' => $request->landlord_response,
        ]);

        // Notify renter
        Notification::create([
            'user_id' => $viewing->user_id,
            'title' => 'Viewing Request ' . ucfirst($request->status),
            'message' => "Your viewing request for '{$viewing->property->title}' has been marked as {$request->status} by the landlord.",
            'type' => 'booking_status',
            'link' => '/renter/viewings',
            'data' => [
                'viewing_id' => $viewing->id,
                'status' => $request->status,
            ],
        ]);

        return $this->sendResponse($viewing, 'Viewing request updated successfully');
    }
}
