<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'lang', 'country_code',
        'name', 'name_in_case',
	    'name_of_case', 'title', 'title_bottom',
	    'announce', 'content', 'meta_description',
	    'bg', 'total', 'changed_fields'
    ];

    public function countrySlug() {
    	return $this->hasOne(
    		'App\CountrySlug',
		    'country_code',
		    'country_code'
	    )->select('slug');
    }
}
