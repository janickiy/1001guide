<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TemplateField extends Model
{
    protected $fillable = [
    	'name', 'type', 'field'
    ];

    function fieldValues () {
    	$this->hasMany(
    		'App\TemplateFieldValue',
		    'field_id', 'id'
	    );
    }
}
