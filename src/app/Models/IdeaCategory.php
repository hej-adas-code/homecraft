<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IdeaCategory extends Model
{
    protected $fillable = ['user_id', 'name', 'color', 'icon'];

    public function ideas()
    {
        return $this->hasMany(Idea::class, 'category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
