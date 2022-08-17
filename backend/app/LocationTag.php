<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LocationTag extends Model
{
	protected $fillable = [
		'location_api_id', 'tag_slug_id', 'lang',
		'title', 'title_bottom',
		'announce', 'content', 'meta_description',
	];
}
