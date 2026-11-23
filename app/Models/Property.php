<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'landlord_id',
        'title',
        'slug',
        'description',
        'property_type',
        'price_per_month',
        'deposit_amount',
        'bedrooms',
        'bathrooms',
        'size_sqm',
        'floor_number',
        'city',
        'khan_district',
        'sangkat_commune',
        'street_address',
        'latitude',
        'longitude',
        'electricity_cost_per_kwh',
        'water_cost_per_m3',
        'wifi_included',
        'parking_available',
        'pet_friendly',
        'furnished_status',
        'status',
        'is_featured',
        'views_count',
        'ai_completeness_score',
        'ai_summary',
    ];

    protected $casts = [
        'price_per_month' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'size_sqm' => 'decimal:2',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'electricity_cost_per_kwh' => 'decimal:2',
        'water_cost_per_m3' => 'decimal:2',
        'wifi_included' => 'boolean',
        'parking_available' => 'boolean',
        'pet_friendly' => 'boolean',
        'is_featured' => 'boolean',
        'views_count' => 'integer',
        'ai_completeness_score' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($property) {
            if (empty($property->slug)) {
                $property->slug = Str::slug($property->title) . '-' . Str::random(6);
            }
        });
    }

    public function landlord()
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    public function amenities()
    {
        return $this->belongsToMany(Amenity::class, 'property_amenity');
    }

    public function images()
    {
        return $this->hasMany(PropertyImage::class)->orderBy('is_primary', 'desc')->orderBy('sort_order', 'asc');
    }

    public function primaryImage()
    {
        return $this->hasOne(PropertyImage::class)->where('is_primary', true);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoritedByUsers()
    {
        return $this->belongsToMany(User::class, 'favorites');
    }

    public function viewingRequests()
    {
        return $this->hasMany(ViewingRequest::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class)->latest();
    }

    public function aiLogs()
    {
        return $this->hasMany(AiAnalysisLog::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function getPrimaryImageUrlAttribute(): string
    {
        $primary = $this->images->firstWhere('is_primary', true) ?? $this->images->first();
        if ($primary && $primary->image_path) {
            return Str::startsWith($primary->image_path, ['http://', 'https://'])
                ? $primary->image_path
                : asset('storage/' . $primary->image_path);
        }
        return 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=800&q=80';
    }

    public function isFavoritedBy(?User $user): bool
    {
        if (!$user) return false;
        return $this->favorites()->where('user_id', $user->id)->exists();
    }
}
