<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plot extends Model
{
    protected $fillable = [
        'user_id','plot_number','address','area','geometry_wkt',
        'lat','lng',
        'house_x','house_y','house_width','house_height','house_rotation',
        'setback_front','setback_back','setback_left','setback_right',
    ];

    protected $casts = [
        'area'           => 'float',
        'lat'            => 'float',
        'lng'            => 'float',
        'house_x'        => 'float',
        'house_y'        => 'float',
        'house_width'    => 'float',
        'house_height'   => 'float',
        'house_rotation' => 'float',
        'setback_front'  => 'float',
        'setback_back'   => 'float',
        'setback_left'   => 'float',
        'setback_right'  => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}