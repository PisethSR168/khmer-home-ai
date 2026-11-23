<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebViewsTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_loads_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200)
            ->assertSee('Room')
            ->assertSee('SR');
    }

    public function test_properties_catalog_loads_successfully(): void
    {
        $landlord = User::factory()->create(['role' => 'landlord']);
        Property::create([
            'landlord_id' => $landlord->id,
            'title' => 'Affordable Clean Room near Wat Bo',
            'slug' => 'affordable-clean-room-wat-bo',
            'description' => 'Great location with balcony and private bath',
            'property_type' => 'room',
            'price_per_month' => 45.00,
            'bedrooms' => 1,
            'bathrooms' => 1,
            'city' => 'Siem Reap',
            'khan_district' => 'Wat Bo',
            'street_address' => 'Street 20, Wat Bo',
            'status' => 'available',
        ]);

        $response = $this->get('/properties');
        $response->assertStatus(200)
            ->assertSee('Affordable Clean Room near Wat Bo')
            ->assertSee('$45');
    }

    public function test_property_details_page_loads(): void
    {
        $landlord = User::factory()->create(['role' => 'landlord']);
        $prop = Property::create([
            'landlord_id' => $landlord->id,
            'title' => 'Comfortable Room in Sala Kamreuk',
            'slug' => 'comfortable-room-sala-kamreuk',
            'description' => 'Beautiful sunset view in Sala Kamreuk',
            'property_type' => 'room',
            'price_per_month' => 75.00,
            'bedrooms' => 1,
            'bathrooms' => 1,
            'city' => 'Siem Reap',
            'khan_district' => 'Sala Kamreuk',
            'street_address' => 'Street 24',
            'status' => 'available',
        ]);

        $response = $this->get('/properties/' . $prop->slug);
        $response->assertStatus(200)
            ->assertSee('Comfortable Room in Sala Kamreuk')
            ->assertSee('Request Viewing');
    }

    public function test_ai_assistant_page_loads(): void
    {
        $response = $this->get('/ai/assistant');
        $response->assertStatus(200)
            ->assertSee('Room SR AI Rental Assistant');
    }
}
