<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    /* @var array */
    protected $fillable = [
        'name',
    ];

    /* @var array */
    protected $guarded = ['api_key'];

}
