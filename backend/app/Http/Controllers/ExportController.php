<?php

namespace App\Http\Controllers;

use App\Exports\LocationExport;
use App\Exports\PoiExport;
use App\Exports\CountryExport;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{

	public function export ($lang, $type) {
		switch ($type) {
			case 'city':
				return Excel::download(
					new LocationExport($lang), "cities_$lang.xlsx"
				);
			case 'poi':
				return Excel::download(
					new PoiExport($lang), "pois_$lang.xlsx"
				);
			default:
				return Excel::download(
					new CountryExport($lang), "countries_$lang.xlsx"
				);
		}

	}

}
