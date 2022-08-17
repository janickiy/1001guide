<?php

namespace App\Exports;

use App\Exports\PageExport;
use App\Location;

class LocationExport extends PageExport
{
	protected static $MODEL = Location::class;

	protected function getModels() {
		return static::$MODEL::where([
			'lang' => $this->lang,
			['type', '!=', 'POI']
		])->select(array_merge(self::FIELDS, self::TEMPLATE_FIELDS, ['api_id']))
		  ->orderBy("name", "asc")
		  ->get();
	}
}
