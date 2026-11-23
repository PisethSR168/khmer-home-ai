<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Favorite;
use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $favorites = Property::whereHas('favorites', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->with(['images', 'amenities', 'landlord'])->get();

        return $this->sendResponse($favorites);
    }

    public function toggle(Request $request, int $propertyId): JsonResponse
    {
        $user = $request->user();
        $property = Property::find($propertyId);

        if (!$property) {
            return $this->sendError('Property not found', [], 404);
        }

        $existing = Favorite::where('user_id', $user->id)
            ->where('property_id', $propertyId)
            ->first();

        if ($existing) {
            $existing->delete();
            $isFavorited = false;
            $message = 'Removed from favorites';
        } else {
            Favorite::create([
                'user_id' => $user->id,
                'property_id' => $propertyId,
            ]);
            $isFavorited = true;
            $message = 'Saved to favorites';
        }

        return $this->sendResponse([
            'property_id' => $propertyId,
            'is_favorited' => $isFavorited,
        ], $message);
    }
}
