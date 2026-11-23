<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\LandlordProfile;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends BaseApiController
{
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:20',
            'role' => 'nullable|string|in:renter,landlord',
            'business_name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors()->toArray(), 422);
        }

        $role = $request->role ?? 'renter';

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $role,
            'password' => Hash::make($request->password),
            'is_active' => true,
        ]);

        if ($role === 'landlord') {
            LandlordProfile::create([
                'user_id' => $user->id,
                'business_name' => $request->business_name ?? $user->name,
                'verification_status' => 'pending',
                'rating' => 5.00,
                'total_reviews' => 0,
            ]);
        }

        $token = $user->createToken('khmer_home_auth_token')->plainTextToken;

        return $this->sendResponse([
            'user' => $user->load('landlordProfile'),
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'Registration successful', 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors()->toArray(), 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->sendError('Invalid credentials. Please verify your email and password.', [], 401);
        }

        if (!$user->is_active) {
            return $this->sendError('Your account has been deactivated. Please contact support.', [], 403);
        }

        $token = $user->createToken('khmer_home_auth_token')->plainTextToken;

        return $this->sendResponse([
            'user' => $user->load('landlordProfile'),
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'Login successful');
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->sendResponse(null, 'Successfully logged out');
    }

    public function user(Request $request): JsonResponse
    {
        $user = $request->user()->load(['landlordProfile']);
        $unreadNotificationsCount = $user->notifications()->where('is_read', false)->count();

        $data = $user->toArray();
        $data['unread_notifications_count'] = $unreadNotificationsCount;

        return $this->sendResponse($data);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:500',
            'avatar' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors()->toArray(), 422);
        }

        $user->update($request->only(['name', 'phone', 'bio', 'avatar']));

        if ($user->isLandlord() && $request->has('business_name')) {
            $user->landlordProfile()->updateOrCreate(
                ['user_id' => $user->id],
                ['business_name' => $request->business_name]
            );
        }

        return $this->sendResponse($user->fresh(['landlordProfile']), 'Profile updated successfully');
    }
}
