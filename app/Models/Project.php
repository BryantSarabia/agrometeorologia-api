<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    const MAX_REQUESTS_BASIC = 10000;
    const MAX_REQUESTS_PRO = 25000;

    /* @var array */
    protected $fillable = [
        'name',
    ];

    /* @var array */
    protected $hidden = ['api_key'];

    public function getLicenseRateLimit(){
        if($this->license === "basic"){
            return self::MAX_REQUESTS_BASIC;
        } elseif ($this->license === "pro"){
            return self::MAX_REQUESTS_PRO;
        } else {
            return 0;
        }
    }

    public function user(){
       return $this->belongsTo('App\Models\User');
    }

    public function requests(){
        return $this->hasMany('App\Models\Request');
    }

}
