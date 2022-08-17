<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LocationSlug extends Model
{
    protected $fillable = ['api_id', 'slug'];
}
