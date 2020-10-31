<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Report extends Model
{
    use HasFactory;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'name',
        'message',
        'lat',
        'lon'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
        'updated_at' => 'datetime:Y-m-d',
        'id' => 'string',
        'user_id' => 'string'
    ];

    protected $dateFormat = 'Y-m-d';

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function formatResponse($distance = null)
    {
        if (!$distance) {
            return [
                'id' => $this->id,
                'user_id' => $this->user_id,
                'name' => $this->name,
                'message' => $this->message,
                'coordinates' => [
                    'lat' => $this->lat,
                    'lon' => $this->lon
                ],
                'created_at' => $this->created_at->format('Y-m-d')
            ];
        } else {
            return [
                'id' => $this->id,
                'user_id' => $this->user_id,
                'name' => $this->name,
                'message' => $this->message,
                'distance' => $distance,
                'coordinates' => [
                    'lat' => $this->lat,
                    'lon' => $this->lon
                ],
                'created_at' => $this->created_at->format('Y-m-d')
            ];
        }

    }

    public static function findNearestReports($lat, $lon, $radius, $from, $to)
    {
        $reports = Report::selectRaw("id, user_id,name, message, lat, lon ,
                         ( 6371 * acos( cos( radians(?) ) *
                           cos( radians( lat ) )
                           * cos( radians( lon ) - radians(?)
                           ) + sin( radians(?) ) *
                           sin( radians( lat ) ) )
                         ) AS distance,  created_at", [$lat, $lon, $lat])
            ->where('created_at', '>=', $from)
            ->where('created_at', '<=', $to)
            ->having("distance", "<", $radius)
            ->orderBy("distance", 'asc')
            ->get();

        $array = collect();

        if ($reports->count() > 0) {
            $reports->each(function ($report) use ($array) {
                $array->push($report->formatResponse($report->distance));
            });
            $array = Collection::wrap(['data' => $array]);

        } else {
            $array['data'] = [];
        }
        return $array;

    }
}
