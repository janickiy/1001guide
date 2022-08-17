<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
	protected $fillable = [
		'api_id', 'api_parent_id', 'lang',
		'name', 'name_in_case', 'type',
		'country_code', 'lat', 'long',
		'bg', 'announce', 'content',
		'title', 'title_bottom', 'meta_description',
		'changed_fields', 'name_of_case'
	];

	public function locationSlug() {
		return $this->hasOne(
			'App\LocationSlug',
			'api_id',
			'api_id'
		)->select('slug');
	}
}
