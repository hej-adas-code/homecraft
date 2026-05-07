<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimelineEntry extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'entry_title',
        'entry_body',
        'image_path',
        'entry_date',
        'entryable_type',
        'entryable_id',
    ];

    protected $casts = [
        'entry_date' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function entryable()
    {
        return $this->morphTo();
    }
}
