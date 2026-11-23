<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\User;
use App\Models\ViewingRequest;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProperties = Property::available()
            ->featured()
            ->with(['images', 'amenities', 'landlord.landlordProfile'])
            ->latest()
            ->take(6)
            ->get();

        $recentProperties = Property::available()
            ->with(['images', 'amenities', 'landlord'])
            ->latest()
            ->take(6)
            ->get();

        $districts = [
            ['name' => 'Sala Kamreuk', 'khmer' => 'សាលាកំរើក', 'count' => Property::where('khan_district', 'Sala Kamreuk')->count(), 'image' => 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=600&q=80'],
            ['name' => 'Svay Dangkum', 'khmer' => 'ស្វាយដង្គំ (Pub St)', 'count' => Property::where('khan_district', 'Svay Dangkum')->count(), 'image' => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=600&q=80'],
            ['name' => 'Wat Bo', 'khmer' => 'វត្តបូព៌', 'count' => Property::where('khan_district', 'Wat Bo')->count(), 'image' => 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=600&q=80'],
            ['name' => 'Slor Kram', 'khmer' => 'ស្លក្រាម (Angkor)', 'count' => Property::where('khan_district', 'Slor Kram')->count(), 'image' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=600&q=80'],
            ['name' => 'Kork Chak', 'khmer' => 'គោកចក', 'count' => Property::where('khan_district', 'Kork Chak')->count(), 'image' => 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=600&q=80'],
        ];

        $stats = [
            'total_listings' => Property::count(),
            'verified_landlords' => User::where('role', 'landlord')->count(),
            'happy_renters' => ViewingRequest::where('status', 'completed')->count() + 85,
            'avg_price' => round(Property::available()->avg('price_per_month') ?? 0),
        ];

        return view('home', compact('featuredProperties', 'recentProperties', 'districts', 'stats'));
    }
}
