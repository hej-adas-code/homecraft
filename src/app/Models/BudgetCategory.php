<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetCategory extends Model
{
    protected $fillable = ['name', 'color', 'budget_limit', 'user_id'];

    public function items()
    {
        return $this->hasMany(BudgetItem::class, 'category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
