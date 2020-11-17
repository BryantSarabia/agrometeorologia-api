<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function projects(){
        return $this->hasMany('App\Models\Project');
    }

    public function reports(){
        return $this->hasMany('App\Models\Report');
    }

    public function locations(){
        return $this->hasMany('App\Models\Location');
    }

    public function generateToken(){
        do{
            $token = str::random(40);
            $check = User::where('token',$token)->first();
        }while($check);

        $this->token = $token;
        $this->save();
    }

    public function deleteToken(){

    }
}
