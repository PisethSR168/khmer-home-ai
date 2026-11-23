<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandlordProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'business_name',
        'id_card_number',
        'id_card_photo',
        'address',
        'telegram_username',
        'verification_status',
        'verified_at',
        'rating',
        'total_reviews',
    ];

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
            'rating' => 'decimal:2',
            'total_reviews' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
