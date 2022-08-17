<?php

namespace App\Exports;

use App\Exports\PageExport;
use App\Country;

class CountryExport extends PageExport
{
	protected static $MODEL = Country::class;

	public function headings(): array
	{
		return [
			'ID',
			'Name',
			'Name In Case',
			'Name Of Case',
			'Changed Fields',
			'Country Code',
			'H1',
			'Announce',
			'H2',
			'Content',
			'Meta Description',
		];
	}
}
