<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class MetaApiConfiguration extends Model
{
    use HasFactory;

    protected $fillable = ['configuration'];

//    protected $casts = [
//      'configuration'  => 'array'
//    ];

}
