<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TagSlug extends Model
{
    protected $fillable = [
    	'get_your_guide_id', 'slug'
    ];
}
