<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LocalSettingTranslation extends Model
{
	protected $fillable = ['setting_id', 'lang', 'value'];
}
