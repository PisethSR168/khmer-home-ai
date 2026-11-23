<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\AiAnalysisLog;
use App\Models\Property;
use App\Models\User;
use App\Models\ViewingRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminController extends BaseApiController
{
    public function dashboard(): JsonResponse
    {
        $stats = [
            'total_properties' => Property::count(),
            'available_properties' => Property::where('status', 'available')->count(),
            'rented_properties' => Property::where('status', 'rented')->count(),
            'total_users' => User::count(),
            'renters_count' => User::where('role', 'renter')->count(),
            'landlords_count' => User::where('role', 'landlord')->count(),
            'total_viewings' => ViewingRequest::count(),
            'pending_viewings' => ViewingRequest::where('status', 'pending')->count(),
            'confirmed_viewings' => ViewingRequest::where('status', 'confirmed')->count(),
            'ai_queries_processed' => AiAnalysisLog::count(),
            'average_rental_price' => round(Property::available()->avg('price_per_month') ?? 0, 2),
        ];

        $recentProperties = Property::with(['landlord', 'images'])->latest()->take(5)->get();
        $recentViewings = ViewingRequest::with(['user', 'property', 'landlord'])->latest()->take(5)->get();

        return $this->sendResponse([
            'stats' => $stats,
            'recent_properties' => $recentProperties,
            'recent_viewings' => $recentViewings,
        ]);
    }

    public function users(Request $request): JsonResponse
    {
        $query = User::with('landlordProfile');

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%");
            });
        }

        $users = $query->latest()->paginate(20);
        return $this->sendResponse($users);
    }

    public function toggleUserStatus(int $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();

        return $this->sendResponse(
            $user,
            $user->is_active ? 'User activated successfully' : 'User suspended successfully'
        );
    }

    public function updatePropertyStatus(Request $request, int $id): JsonResponse
    {
        $property = Property::findOrFail($id);

        if ($request->has('status')) {
            $property->status = $request->status;
        }

        if ($request->has('is_featured')) {
            $property->is_featured = $request->boolean('is_featured');
        }

        $property->save();

        return $this->sendResponse($property, 'Property status updated');
    }
}
