<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $fillable = ['user_id', 'estimate_id', 'title', 'contractor_name', 'amount', 'status', 'description', 'valid_until', 'file_path'];

    protected $casts = [
        'valid_until' => 'date',
        'amount' => 'decimal:2',
    ];

    public function estimate()
    {
        return $this->belongsTo(Estimate::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
