<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AiAnalysisLog;
use App\Models\LandlordProfile;
use App\Models\Property;
use App\Models\User;
use App\Models\ViewingRequest;
use Illuminate\Http\Request;

class WebAdminController extends Controller
{
    public function dashboard()
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
            'ai_logs_count' => AiAnalysisLog::count(),
            'avg_price' => round(Property::available()->avg('price_per_month') ?? 0, 2),
        ];

        $properties = Property::with(['landlord', 'images'])->latest()->paginate(10);
        $users = User::latest()->paginate(10);
        $pendingLandlords = LandlordProfile::where('verification_status', 'pending')->with('user')->get();
        $aiLogs = AiAnalysisLog::latest()->take(10)->get();

        return view('admin.dashboard', compact('stats', 'properties', 'users', 'pendingLandlords', 'aiLogs'));
    }

    public function toggleUser(int $id)
    {
        $user = User::findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();

        return back()->with('success', 'User status updated to ' . ($user->is_active ? 'Active' : 'Suspended'));
    }

    public function toggleFeatured(int $id)
    {
        $property = Property::findOrFail($id);
        $property->is_featured = !$property->is_featured;
        $property->save();

        return back()->with('success', 'Property featured status updated.');
    }

    public function verifyLandlord(Request $request, int $id)
    {
        $profile = LandlordProfile::findOrFail($id);
        $status = $request->input('status', 'verified');
        $profile->update([
            'verification_status' => $status,
            'verified_at' => $status === 'verified' ? now() : null,
        ]);

        return back()->with('success', "Landlord profile marked as {$status}.");
    }
}
