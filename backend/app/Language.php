<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
	public const DEFAULT_LANG = 'en';

	protected $fillable = ['code', 'name', 'html_code'];
}
