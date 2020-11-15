<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['lat', 'lon', 'radius', 'user_id'];

    public function user(){
        return $this->belongsTo('App\Models\User');
    }

    public function formatResponse(){
        return [
            'id' => (string) $this->id,
            'user_id' => (string) $this->user_id,
            'radius' => $this->radius,
            'coordinates' => [
                'lat' => $this->lat,
                'lon' => $this->lon
            ]
        ];
    }
}
