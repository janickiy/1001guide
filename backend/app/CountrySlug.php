<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CountrySlug extends Model
{
    protected $fillable = ['country_code', 'slug'];
}
