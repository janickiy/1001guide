<?php

namespace App\Http\Controllers;

use App\Country;
use App\Location;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PageImport;

class ImportController extends Controller
{

	public function import ($lang, $type, Request $request) {
		$file = $request->file('xls');
		if ( !$file )
			return response()->json([
				'success' => false,
				'message' => 'File was not uploaded'
			]);

		// choose what to update
		switch ($type) {
			case 'country':
				$model = Country::class;
				break;
			default:
				$model = Location::class;
				break;
		}

		// make an import
		Excel::import(new PageImport($lang, $model), $file);

		return response()->json([
			'success' => true,
		]);
	}

}
