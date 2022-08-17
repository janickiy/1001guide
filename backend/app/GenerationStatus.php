<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GenerationStatus extends Model
{
    protected $fillable = [
    	'type', 'status', 'current', 'total'
    ];
}
