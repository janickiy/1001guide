<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LocalSetting extends Model
{
	public function translations() {
		return $this->hasMany('App\LocalSettingTranslation', ['setting_id']);
	}
}
