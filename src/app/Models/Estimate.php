<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estimate extends Model
{
    protected $fillable = ['user_id', 'title', 'description', 'status', 'total', 'valid_until'];

    protected $casts = [
        'valid_until' => 'date',
        'total' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(EstimateItem::class);
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
