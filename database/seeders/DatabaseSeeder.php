<?php

namespace Database\Seeders;

use App\Models\Amenity;
use App\Models\LandlordProfile;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\Review;
use App\Models\User;
use App\Models\ViewingRequest;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with real rooms in Siem Reap priced between $30 and $100 / month.
     */
    public function run(): void
    {
        // 1. Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@khmerhome.ai'],
            [
                'name' => 'Admin Room SR',
                'phone' => '+855 12 888 999',
                'role' => 'admin',
                'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=300&q=80',
                'bio' => 'Room SR Platform Administrator - Siem Reap, Cambodia.',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );

        // 2. Siem Reap Landlords
        $landlord1 = User::firstOrCreate(
            ['email' => 'sokha.landlord@khmerhome.ai'],
            [
                'name' => 'Sokha Chea',
                'phone' => '+855 12 345 678',
                'role' => 'landlord',
                'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=300&q=80',
                'bio' => 'Local landlord offering clean, affordable rental rooms ($35 - $100/mo) in Sala Kamreuk, Wat Bo, and Chreav.',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
        LandlordProfile::firstOrCreate(
            ['user_id' => $landlord1->id],
            [
                'business_name' => 'Sokha Siem Reap Residences',
                'id_card_number' => 'KH-SR-0123984',
                'address' => 'Wat Bo Road, Sala Kamreuk, Krong Siem Reap',
                'telegram_username' => '@sokhachea_sr',
                'verification_status' => 'verified',
                'verified_at' => now(),
                'rating' => 4.95,
                'total_reviews' => 28,
            ]
        );

        $landlord2 = User::firstOrCreate(
            ['email' => 'chantha.landlord@khmerhome.ai'],
            [
                'name' => 'Chantha Pich',
                'phone' => '+855 89 776 655',
                'role' => 'landlord',
                'avatar' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=300&q=80',
                'bio' => 'Affordable and secure student and worker rooms in Svay Dangkum and Slor Kram, Siem Reap.',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
        LandlordProfile::firstOrCreate(
            ['user_id' => $landlord2->id],
            [
                'business_name' => 'Chantha Budget Rooms SR',
                'id_card_number' => 'KH-SR-0987654',
                'address' => 'Sok San Road, Svay Dangkum, Krong Siem Reap',
                'telegram_username' => '@chantha_sr_rooms',
                'verification_status' => 'verified',
                'verified_at' => now(),
                'rating' => 4.90,
                'total_reviews' => 22,
            ]
        );

        // 3. Renter Users
        $renter1 = User::firstOrCreate(
            ['email' => 'visal.renter@khmerhome.ai'],
            [
                'name' => 'Visal Meng',
                'phone' => '+855 93 111 222',
                'role' => 'renter',
                'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=300&q=80',
                'bio' => 'Looking for an affordable, quiet room in Siem Reap with fast Wi-Fi.',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );

        $renter2 = User::firstOrCreate(
            ['email' => 'bopha.renter@khmerhome.ai'],
            [
                'name' => 'Bopha Keo',
                'phone' => '+855 10 333 444',
                'role' => 'renter',
                'avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=300&q=80',
                'bio' => 'Hotel worker in Siem Reap looking for an affordable room near town center.',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );

        // 4. Amenities
        $amenityData = [
            ['name' => 'Air Conditioning', 'slug' => 'air-conditioning', 'icon' => 'wind', 'category' => 'comfort'],
            ['name' => 'High-Speed Wi-Fi', 'slug' => 'high-speed-wifi', 'icon' => 'wifi', 'category' => 'facility'],
            ['name' => 'Private Balcony', 'slug' => 'private-balcony', 'icon' => 'sun', 'category' => 'comfort'],
            ['name' => 'Private Bathroom', 'slug' => 'private-bathroom', 'icon' => 'bath', 'category' => 'comfort'],
            ['name' => 'Hot Water Shower', 'slug' => 'hot-water-shower', 'icon' => 'flame', 'category' => 'comfort'],
            ['name' => 'Ceiling Fan', 'slug' => 'ceiling-fan', 'icon' => 'fan', 'category' => 'comfort'],
            ['name' => 'Study Desk & Chair', 'slug' => 'study-desk', 'icon' => 'laptop', 'category' => 'facility'],
            ['name' => 'Wardrobe / Closet', 'slug' => 'wardrobe', 'icon' => 'archive', 'category' => 'facility'],
            ['name' => 'Refrigerator', 'slug' => 'refrigerator', 'icon' => 'archive', 'category' => 'facility'],
            ['name' => '24/7 Security & CCTV', 'slug' => 'security-cctv', 'icon' => 'shield-check', 'category' => 'security'],
            ['name' => 'Gated Motorbike Parking', 'slug' => 'motorbike-parking', 'icon' => 'bike', 'category' => 'facility'],
            ['name' => 'Bicycle Parking', 'slug' => 'bicycle-parking', 'icon' => 'bike', 'category' => 'facility'],
            ['name' => 'Pet Friendly', 'slug' => 'pet-friendly', 'icon' => 'paw-print', 'category' => 'rule'],
            ['name' => 'Kitchenette Counter', 'slug' => 'kitchen-equipped', 'icon' => 'utensils', 'category' => 'facility'],
        ];

        $amenities = [];
        foreach ($amenityData as $item) {
            $amenities[$item['slug']] = Amenity::firstOrCreate(
                ['slug' => $item['slug']],
                $item
            );
        }

        // 5. Real Rooms in Siem Reap priced between $30 and $100 / month
        $propertiesData = [
            [
                'landlord_id' => $landlord1->id,
                'title' => 'Affordable Clean Room near Wat Bo - Siem Reap',
                'slug' => 'affordable-clean-room-wat-bo-siem-reap',
                'description' => "Clean, bright, budget-friendly private room in central Wat Bo Village, Siem Reap. Fully tiled floor, ceiling fan, private bathroom with shower, secure indoor motorbike parking gate, and high-speed Wi-Fi. Quiet atmosphere, 3 minutes walk to riverfront cafes and fresh local market. Very economical EDC electricity ($0.20/kWh) and water ($0.25/m³).",
                'property_type' => 'room',
                'price_per_month' => 35.00,
                'deposit_amount' => 35.00,
                'bedrooms' => 1,
                'bathrooms' => 1,
                'size_sqm' => 18.00,
                'floor_number' => 2,
                'city' => 'Siem Reap',
                'khan_district' => 'Wat Bo',
                'sangkat_commune' => 'Sala Kamreuk',
                'street_address' => 'Street 20, Wat Bo Village, Siem Reap',
                'latitude' => 13.3551,
                'longitude' => 103.8618,
                'electricity_cost_per_kwh' => 0.20,
                'water_cost_per_m3' => 0.25,
                'wifi_included' => true,
                'parking_available' => true,
                'pet_friendly' => false,
                'furnished_status' => 'semi_furnished',
                'status' => 'available',
                'is_featured' => true,
                'views_count' => 640,
                'ai_completeness_score' => 96,
                'ai_summary' => "Unbeatable $35/month value in Wat Bo village. Private bathroom, tiled floor, free Wi-Fi, and official EDC rate ($0.20/kWh).",
                'amenities' => ['high-speed-wifi', 'private-bathroom', 'ceiling-fan', 'security-cctv', 'motorbike-parking', 'bicycle-parking'],
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?auto=format&fit=crop&w=1200&q=80', 'is_primary' => true, 'caption' => 'Clean tiled private room with double bed and natural window light'],
                ],
            ],
            [
                'landlord_id' => $landlord1->id,
                'title' => 'Peaceful Worker & Student Room - Chreav, Siem Reap',
                'slug' => 'peaceful-worker-student-room-chreav',
                'description' => "Quiet, peaceful room in Chreav commune, just 8 minutes drive from Siem Reap town center. Surrounding countryside breeze, double mattress, study table, ceiling fan, private bathroom, and safe gated compound. Perfect for budget-conscious remote workers, students, or hospitality staff.",
                'property_type' => 'room',
                'price_per_month' => 45.00,
                'deposit_amount' => 45.00,
                'bedrooms' => 1,
                'bathrooms' => 1,
                'size_sqm' => 20.00,
                'floor_number' => 1,
                'city' => 'Siem Reap',
                'khan_district' => 'Chreav',
                'sangkat_commune' => 'Chreav',
                'street_address' => 'Ring Road / Chreav Market Area',
                'latitude' => 13.3312,
                'longitude' => 103.8795,
                'electricity_cost_per_kwh' => 0.20,
                'water_cost_per_m3' => 0.25,
                'wifi_included' => true,
                'parking_available' => true,
                'pet_friendly' => true,
                'furnished_status' => 'semi_furnished',
                'status' => 'available',
                'is_featured' => true,
                'views_count' => 510,
                'ai_completeness_score' => 94,
                'ai_summary' => "High-value $45/month room in green Chreav commune with private bathroom, fast internet, and quiet nights.",
                'amenities' => ['high-speed-wifi', 'private-bathroom', 'ceiling-fan', 'study-desk', 'motorbike-parking', 'pet-friendly'],
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=1200&q=80', 'is_primary' => true, 'caption' => 'Spacious room with study table, double bed, and quiet surroundings'],
                ],
            ],
            [
                'landlord_id' => $landlord2->id,
                'title' => 'Clean Cozy Room with En-Suite Bath - Svay Dangkum, Siem Reap',
                'slug' => 'clean-cozy-room-ensuite-bath-svay-dangkum',
                'description' => "Cozy and secure private room in Svay Dangkum, 5 minutes walk to Old Market and Sok San Road. Features a solid wooden bed with clean mattress, wardrobe, study desk, private en-suite bathroom with hot water shower, ceiling fan, and gated parking with CCTV security.",
                'property_type' => 'room',
                'price_per_month' => 60.00,
                'deposit_amount' => 60.00,
                'bedrooms' => 1,
                'bathrooms' => 1,
                'size_sqm' => 22.00,
                'floor_number' => 2,
                'city' => 'Siem Reap',
                'khan_district' => 'Svay Dangkum',
                'sangkat_commune' => 'Svay Dangkum',
                'street_address' => 'Sok San Road / Night Market alley',
                'latitude' => 13.3562,
                'longitude' => 103.8519,
                'electricity_cost_per_kwh' => 0.20,
                'water_cost_per_m3' => 0.30,
                'wifi_included' => true,
                'parking_available' => true,
                'pet_friendly' => false,
                'furnished_status' => 'fully_furnished',
                'status' => 'available',
                'is_featured' => true,
                'views_count' => 720,
                'ai_completeness_score' => 97,
                'ai_summary' => "Prime central Svay Dangkum room at $60/month. Walking distance to Pub Street, en-suite hot shower, and fast Wi-Fi.",
                'amenities' => ['high-speed-wifi', 'private-bathroom', 'hot-water-shower', 'ceiling-fan', 'study-desk', 'wardrobe', 'security-cctv', 'motorbike-parking'],
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=1200&q=80', 'is_primary' => true, 'caption' => 'Comfortable double bed room with en-suite bath and study corner'],
                ],
            ],
            [
                'landlord_id' => $landlord2->id,
                'title' => 'Bright Room with Balcony near Phsar Leu - Slor Kram, Siem Reap',
                'slug' => 'bright-room-balcony-phsar-leu-slor-kram',
                'description' => "Bright room on the 2nd floor with private sunset balcony and large windows. Located in quiet Slor Kram near Phsar Leu market and Angkor National Museum. Features tiled bathroom, study desk, fan, double bed, clean wardrobe, and 24/7 camera monitoring.",
                'property_type' => 'room',
                'price_per_month' => 75.00,
                'deposit_amount' => 75.00,
                'bedrooms' => 1,
                'bathrooms' => 1,
                'size_sqm' => 25.00,
                'floor_number' => 2,
                'city' => 'Siem Reap',
                'khan_district' => 'Slor Kram',
                'sangkat_commune' => 'Slor Kram',
                'street_address' => 'Near Charles de Gaulle / Phsar Leu Market',
                'latitude' => 13.3685,
                'longitude' => 103.8642,
                'electricity_cost_per_kwh' => 0.20,
                'water_cost_per_m3' => 0.25,
                'wifi_included' => true,
                'parking_available' => true,
                'pet_friendly' => false,
                'furnished_status' => 'fully_furnished',
                'status' => 'available',
                'is_featured' => true,
                'views_count' => 430,
                'ai_completeness_score' => 95,
                'ai_summary' => "Sunny room with private balcony in Slor Kram at $75/mo. Clean, safe, and close to both town and Angkor park.",
                'amenities' => ['high-speed-wifi', 'private-balcony', 'private-bathroom', 'hot-water-shower', 'ceiling-fan', 'study-desk', 'wardrobe', 'security-cctv', 'motorbike-parking'],
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1200&q=80', 'is_primary' => true, 'caption' => 'Warm, naturally lit bedroom with private balcony access'],
                ],
            ],
            [
                'landlord_id' => $landlord1->id,
                'title' => 'Modern Air-Conditioned Room - Sala Kamreuk, Siem Reap',
                'slug' => 'modern-aircon-room-sala-kamreuk',
                'description' => "Comfortable air-conditioned private room in upscale, leafy Sala Kamreuk. Includes inverter air conditioner, hot water rain shower, queen-size wooden bed with spring mattress, clothes closet, refrigerator, and ultra-fast Wi-Fi. 4 minutes bicycle ride to Wat Bo cafes.",
                'property_type' => 'room',
                'price_per_month' => 85.00,
                'deposit_amount' => 85.00,
                'bedrooms' => 1,
                'bathrooms' => 1,
                'size_sqm' => 26.00,
                'floor_number' => 1,
                'city' => 'Siem Reap',
                'khan_district' => 'Sala Kamreuk',
                'sangkat_commune' => 'Sala Kamreuk',
                'street_address' => 'Wat Bo Road / Street 24, Sala Kamreuk',
                'latitude' => 13.3538,
                'longitude' => 103.8660,
                'electricity_cost_per_kwh' => 0.22,
                'water_cost_per_m3' => 0.30,
                'wifi_included' => true,
                'parking_available' => true,
                'pet_friendly' => true,
                'furnished_status' => 'fully_furnished',
                'status' => 'available',
                'is_featured' => true,
                'views_count' => 590,
                'ai_completeness_score' => 98,
                'ai_summary' => "Full aircon comfort in prime Sala Kamreuk at $85/month. Refrigerator, hot rain shower, and peaceful expat neighborhood.",
                'amenities' => ['air-conditioning', 'high-speed-wifi', 'private-bathroom', 'hot-water-shower', 'refrigerator', 'wardrobe', 'study-desk', 'security-cctv', 'motorbike-parking', 'pet-friendly'],
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1616046229478-9901c5536a45?auto=format&fit=crop&w=1200&q=80', 'is_primary' => true, 'caption' => 'Modern air-conditioned room with solid wooden headboard and crisp sheets'],
                ],
            ],
            [
                'landlord_id' => $landlord2->id,
                'title' => 'Furnished Room with Aircon & Fridge near Pub Street - Svay Dangkum',
                'slug' => 'furnished-room-aircon-fridge-pub-street',
                'description' => "Fully furnished air-conditioned room located right behind Night Market / Sok San Road. Equipped with private refrigerator, remote control aircon, hot shower, study desk, wardrobe, and double bed. Perfect for remote workers and town center workers wanting zero commute.",
                'property_type' => 'room',
                'price_per_month' => 95.00,
                'deposit_amount' => 95.00,
                'bedrooms' => 1,
                'bathrooms' => 1,
                'size_sqm' => 28.00,
                'floor_number' => 2,
                'city' => 'Siem Reap',
                'khan_district' => 'Svay Dangkum',
                'sangkat_commune' => 'Svay Dangkum',
                'street_address' => 'Night Market Road / Sok San, Siem Reap',
                'latitude' => 13.3575,
                'longitude' => 103.8530,
                'electricity_cost_per_kwh' => 0.22,
                'water_cost_per_m3' => 0.30,
                'wifi_included' => true,
                'parking_available' => true,
                'pet_friendly' => false,
                'furnished_status' => 'fully_furnished',
                'status' => 'available',
                'is_featured' => true,
                'views_count' => 680,
                'ai_completeness_score' => 98,
                'ai_summary' => "Top central convenience at $95/month with aircon, fridge, hot shower, and 2 minutes walk to Pub Street.",
                'amenities' => ['air-conditioning', 'high-speed-wifi', 'private-bathroom', 'hot-water-shower', 'refrigerator', 'wardrobe', 'study-desk', 'security-cctv', 'motorbike-parking'],
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=1200&q=80', 'is_primary' => true, 'caption' => 'Clean, air-conditioned room with study desk, curtains, and private fridge'],
                ],
            ],
            [
                'landlord_id' => $landlord1->id,
                'title' => 'Garden Studio Room with Private Entrance & Aircon - Sala Kamreuk',
                'slug' => 'garden-studio-private-entrance-aircon-sala-kamreuk',
                'description' => "Charming ground-floor studio room with private entrance, small food preparation counter, air conditioning, modern bathroom, and tropical garden access in Sala Kamreuk. High-speed Wi-Fi included. Quiet, leafy, and only 4 minutes to Wat Bo Road.",
                'property_type' => 'studio',
                'price_per_month' => 100.00,
                'deposit_amount' => 100.00,
                'bedrooms' => 1,
                'bathrooms' => 1,
                'size_sqm' => 32.00,
                'floor_number' => 1,
                'city' => 'Siem Reap',
                'khan_district' => 'Sala Kamreuk',
                'sangkat_commune' => 'Sala Kamreuk',
                'street_address' => 'Wat Bo Road / Street 26, Sala Kamreuk',
                'latitude' => 13.3510,
                'longitude' => 103.8682,
                'electricity_cost_per_kwh' => 0.22,
                'water_cost_per_m3' => 0.30,
                'wifi_included' => true,
                'parking_available' => true,
                'pet_friendly' => true,
                'furnished_status' => 'fully_furnished',
                'status' => 'available',
                'is_featured' => true,
                'views_count' => 790,
                'ai_completeness_score' => 99,
                'ai_summary' => "The best $100/mo studio room in Sala Kamreuk. Private entrance, aircon, food prep counter, hot rain shower, and garden breeze.",
                'amenities' => ['air-conditioning', 'high-speed-wifi', 'private-balcony', 'private-bathroom', 'hot-water-shower', 'kitchen-equipped', 'refrigerator', 'wardrobe', 'security-cctv', 'motorbike-parking', 'pet-friendly'],
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?auto=format&fit=crop&w=1200&q=80', 'is_primary' => true, 'caption' => 'Spacious $100 studio room with comfortable double bed, reading lamps, and garden patio'],
                ],
            ],
            [
                'landlord_id' => $landlord2->id,
                'title' => 'Tranquil Budget Room near Angkor Road - Kork Chak, Siem Reap',
                'slug' => 'tranquil-budget-room-kork-chak-siem-reap',
                'description' => "Tranquil and affordable private room in Kork Chak, along the quiet route to the Angkor temples. Features ceiling fan, private bathroom with shower, desk, double bed, and clean tile floor. Gated parking for motorbikes and bicycles.",
                'property_type' => 'room',
                'price_per_month' => 50.00,
                'deposit_amount' => 50.00,
                'bedrooms' => 1,
                'bathrooms' => 1,
                'size_sqm' => 20.00,
                'floor_number' => 1,
                'city' => 'Siem Reap',
                'khan_district' => 'Kork Chak',
                'sangkat_commune' => 'Kork Chak',
                'street_address' => 'Near Angkor Conservation / Ring Road',
                'latitude' => 13.3740,
                'longitude' => 103.8390,
                'electricity_cost_per_kwh' => 0.20,
                'water_cost_per_m3' => 0.25,
                'wifi_included' => true,
                'parking_available' => true,
                'pet_friendly' => true,
                'furnished_status' => 'semi_furnished',
                'status' => 'available',
                'is_featured' => false,
                'views_count' => 380,
                'ai_completeness_score' => 93,
                'ai_summary' => "Peaceful $50/month room surrounded by trees in Kork Chak. Free Wi-Fi and official low utility rates.",
                'amenities' => ['high-speed-wifi', 'private-bathroom', 'ceiling-fan', 'study-desk', 'motorbike-parking', 'bicycle-parking', 'pet-friendly'],
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=1200&q=80', 'is_primary' => true, 'caption' => 'Quiet, clean room with double bed and natural ventilation'],
                ],
            ],
        ];

        foreach ($propertiesData as $pData) {
            $amenitySlugs = $pData['amenities'] ?? [];
            $images = $pData['images'] ?? [];
            unset($pData['amenities'], $pData['images']);

            $prop = Property::firstOrCreate(
                ['slug' => $pData['slug']],
                $pData
            );

            // Attach amenities
            $amenityIds = [];
            foreach ($amenitySlugs as $slug) {
                if (isset($amenities[$slug])) {
                    $amenityIds[] = $amenities[$slug]->id;
                }
            }
            $prop->amenities()->sync($amenityIds);

            // Attach images
            foreach ($images as $idx => $img) {
                PropertyImage::firstOrCreate(
                    ['property_id' => $prop->id, 'image_path' => $img['url']],
                    [
                        'is_primary' => $img['is_primary'],
                        'caption' => $img['caption'],
                        'image_hash' => md5($img['url']),
                        'ai_labels' => ['bedroom', 'clean', 'siem reap', 'budget room', 'interior'],
                        'ai_quality_score' => 0.95,
                        'sort_order' => $idx,
                    ]
                );
            }
        }

        // 6. Viewing request sample in Siem Reap
        $firstProp = Property::where('slug', 'affordable-clean-room-wat-bo-siem-reap')->first();
        if ($firstProp) {
            ViewingRequest::firstOrCreate(
                [
                    'user_id' => $renter1->id,
                    'property_id' => $firstProp->id,
                ],
                [
                    'landlord_id' => $firstProp->landlord_id,
                    'preferred_date' => now()->addDays(2)->toDateString(),
                    'preferred_time' => '10:00 AM',
                    'status' => 'confirmed',
                    'contact_phone' => '+855 93 111 222',
                    'renter_note' => 'I would like to inspect the room and test the Wi-Fi speed.',
                    'landlord_response' => 'Welcome! I will meet you at the Wat Bo property gate at 10:00 AM.',
                ]
            );

            Review::firstOrCreate(
                [
                    'user_id' => $renter2->id,
                    'property_id' => $firstProp->id,
                ],
                [
                    'rating' => 5,
                    'comment' => 'Great value for only $35/month. Clean room, quiet at night, and landlord is very helpful!',
                ]
            );
        }
    }
}
