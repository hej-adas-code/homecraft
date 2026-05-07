<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = ['user_id', 'title', 'category', 'file_path', 'file_name', 'file_size', 'description'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
