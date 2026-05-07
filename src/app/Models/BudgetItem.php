<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetItem extends Model
{
    protected $fillable = ['user_id', 'category_id', 'name', 'planned_amount', 'actual_amount', 'description', 'date', 'type'];

    protected $casts = [
        'date' => 'date',
        'planned_amount' => 'decimal:2',
        'actual_amount' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(BudgetCategory::class, 'category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
