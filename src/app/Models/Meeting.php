<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    protected $fillable = [
        "user_id","title","contractor_name","contact_id",
        "meeting_at","location","agenda","notes","status",
    ];

    protected $casts = ["meeting_at" => "datetime"];

    public function user()   { return $this->belongsTo(User::class); }
    public function contact(){ return $this->belongsTo(Contact::class); }
}