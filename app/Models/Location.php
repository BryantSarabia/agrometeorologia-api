<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

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

    public static function findNearestReports($lat, $lon, $radius)
    {
        $reports = Report::selectRaw("id, user_id,name, message, lat, lon ,
                         ( 6371 * acos( cos( radians(?) ) *
                           cos( radians( lat ) )
                           * cos( radians( lon ) - radians(?)
                           ) + sin( radians(?) ) *
                           sin( radians( lat ) ) )
                         ) AS distance,  created_at", [$lat, $lon, $lat])
            ->where('created_at', '=', date('Y-m-d'))
            ->having("distance", "<", $radius)
            ->orderBy("distance", 'asc')
            ->get();

        return $reports;
    }


}
