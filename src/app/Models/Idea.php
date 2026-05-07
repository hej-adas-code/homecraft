<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Idea extends Model
{
    protected $fillable = ['user_id', 'category_id', 'title', 'description', 'image_path', 'image_url', 'link', 'tags'];

    public function category()
    {
        return $this->belongsTo(IdeaCategory::class, 'category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
