<?php

namespace Tests\Feature;

use App\Models\Amenity;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiV1Test extends TestCase
{
    use RefreshDatabase;

    public function test_can_register_user_via_api(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'John Renter',
            'email' => 'john@test.com',
            'password' => 'password123',
            'phone' => '+855 12 345 678',
            'role' => 'renter',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['user', 'token', 'token_type'],
            ]);
    }

    public function test_can_login_and_get_user(): void
    {
        $user = User::factory()->create([
            'email' => 'login@test.com',
            'password' => bcrypt('password123'),
            'role' => 'renter',
        ]);

        $loginResponse = $this->postJson('/api/v1/auth/login', [
            'email' => 'login@test.com',
            'password' => 'password123',
        ]);

        $loginResponse->assertStatus(200)
            ->assertJsonPath('success', true);

        $token = $loginResponse->json('data.token');

        $userResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/auth/user');

        $userResponse->assertStatus(200)
            ->assertJsonPath('data.email', 'login@test.com');
    }

    public function test_can_fetch_properties_list(): void
    {
        $landlord = User::factory()->create(['role' => 'landlord']);

        Property::create([
            'landlord_id' => $landlord->id,
            'title' => 'Test Studio BKK1',
            'slug' => 'test-studio-bkk1',
            'description' => 'Test description for unit in BKK1',
            'property_type' => 'studio',
            'price_per_month' => 350.00,
            'bedrooms' => 1,
            'bathrooms' => 1,
            'city' => 'Phnom Penh',
            'khan_district' => 'Boeng Keng Kang',
            'street_address' => 'St 302',
            'status' => 'available',
        ]);

        $response = $this->getJson('/api/v1/properties');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.total', 1);
    }

    public function test_ai_smart_search_returns_relevant_data(): void
    {
        $landlord = User::factory()->create(['role' => 'landlord']);

        Property::create([
            'landlord_id' => $landlord->id,
            'title' => 'Affordable Room Tuol Kouk',
            'slug' => 'affordable-room-tk',
            'description' => 'Great student room near RUPP',
            'property_type' => 'room',
            'price_per_month' => 150.00,
            'bedrooms' => 1,
            'bathrooms' => 1,
            'city' => 'Phnom Penh',
            'khan_district' => 'Tuol Kouk',
            'street_address' => 'St 289',
            'status' => 'available',
        ]);

        $response = $this->postJson('/api/v1/ai/smart-search', [
            'query' => 'Room in Tuol Kouk under $200',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'data' => ['query', 'extracted_criteria', 'explanation', 'properties'],
            ]);
    }
}
