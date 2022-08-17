<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = [
    	'get_your_guide_id', 'lang', 'name'
    ];

	public function countrySlug() {
		return $this->hasOne(
			'App\TagSlug',
			'get_your_guide_id',
			'get_your_guide_id'
		)->select('slug');
	}
}
