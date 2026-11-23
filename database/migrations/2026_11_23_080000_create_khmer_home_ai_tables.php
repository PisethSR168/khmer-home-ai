<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Landlord Profiles
        Schema::create('landlord_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('business_name')->nullable();
            $table->string('id_card_number')->nullable();
            $table->string('id_card_photo')->nullable();
            $table->string('address')->nullable();
            $table->string('telegram_username')->nullable();
            $table->string('verification_status')->default('pending'); // pending, verified, rejected
            $table->timestamp('verified_at')->nullable();
            $table->decimal('rating', 3, 2)->default(5.00);
            $table->integer('total_reviews')->default(0);
            $table->timestamps();
        });

        // Amenities
        Schema::create('amenities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->default('check'); // Lucide icon name
            $table->string('category')->default('facility'); // comfort, facility, security, rule
            $table->timestamps();
        });

        // Properties & Rooms
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landlord_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('description');
            $table->string('property_type')->default('room'); // room, studio, apartment, condo, house
            $table->decimal('price_per_month', 10, 2);
            $table->decimal('deposit_amount', 10, 2)->nullable();
            $table->integer('bedrooms')->default(1);
            $table->integer('bathrooms')->default(1);
            $table->decimal('size_sqm', 8, 2)->nullable();
            $table->integer('floor_number')->nullable();
            $table->string('city')->default('Phnom Penh');
            $table->string('khan_district'); // e.g. Chamkar Mon, Tuol Kouk, Boeng Keng Kang
            $table->string('sangkat_commune')->nullable();
            $table->string('street_address');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('electricity_cost_per_kwh', 8, 2)->nullable();
            $table->decimal('water_cost_per_m3', 8, 2)->nullable();
            $table->boolean('wifi_included')->default(false);
            $table->boolean('parking_available')->default(true);
            $table->boolean('pet_friendly')->default(false);
            $table->string('furnished_status')->default('semi_furnished'); // unfurnished, semi_furnished, fully_furnished
            $table->string('status')->default('available'); // available, rented, pending_approval, rejected, inactive
            $table->boolean('is_featured')->default(false);
            $table->unsignedBigInteger('views_count')->default(0);
            $table->integer('ai_completeness_score')->default(85);
            $table->text('ai_summary')->nullable();
            $table->timestamps();
        });

        // Property Amenities Pivot Table
        Schema::create('property_amenity', function (Blueprint $table) {
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('amenity_id')->constrained()->cascadeOnDelete();
            $table->primary(['property_id', 'amenity_id']);
        });

        // Property Images
        Schema::create('property_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->string('image_path');
            $table->boolean('is_primary')->default(false);
            $table->string('caption')->nullable();
            $table->string('image_hash')->nullable(); // duplicate detection
            $table->json('ai_labels')->nullable();
            $table->decimal('ai_quality_score', 3, 2)->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Favorites
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->unique(['user_id', 'property_id']);
            $table->timestamps();
        });

        // Viewing Requests & Bookings
        Schema::create('viewing_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // renter
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('landlord_id')->constrained('users')->cascadeOnDelete();
            $table->date('preferred_date');
            $table->string('preferred_time');
            $table->string('status')->default('pending'); // pending, confirmed, completed, cancelled, rejected
            $table->string('contact_phone');
            $table->text('renter_note')->nullable();
            $table->text('landlord_response')->nullable();
            $table->timestamps();
        });

        // Messages / Direct Chat
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('receiver_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('property_id')->nullable()->constrained()->nullOnDelete();
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        // Notifications
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('message');
            $table->string('type')->default('system'); // viewing_request, booking_status, chat_message, ai_recommendation, system
            $table->string('link')->nullable();
            $table->json('data')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        // AI Analysis Logs
        Schema::create('ai_analysis_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('property_id')->nullable()->constrained()->nullOnDelete();
            $table->string('feature'); // smart_search, smart_recommendations, rental_assistant, description_assistant, completeness_check, photo_analysis, duplicate_detection
            $table->text('prompt_input')->nullable();
            $table->longText('response_output')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        // Reviews
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->integer('rating')->default(5);
            $table->text('comment');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('ai_analysis_logs');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('viewing_requests');
        Schema::dropIfExists('favorites');
        Schema::dropIfExists('property_images');
        Schema::dropIfExists('property_amenity');
        Schema::dropIfExists('properties');
        Schema::dropIfExists('amenities');
        Schema::dropIfExists('landlord_profiles');
    }
};
