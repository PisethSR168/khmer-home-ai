<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PropertyImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'image_path',
        'is_primary',
        'caption',
        'image_hash',
        'ai_labels',
        'ai_quality_score',
        'sort_order',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'ai_labels' => 'array',
        'ai_quality_score' => 'decimal:2',
        'sort_order' => 'integer',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function getUrlAttribute(): string
    {
        if (Str::startsWith($this->image_path, ['http://', 'https://'])) {
            return $this->image_path;
        }
        return asset('storage/' . $this->image_path);
    }
}
