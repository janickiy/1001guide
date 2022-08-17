<?php

namespace App\Http\Controllers;

use App\Currency;
use App\Language;
use Illuminate\Http\Request;

class CoreController extends Controller
{

	/**
	 * Return locale JSON
	 *
	 * @param string $lang
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function frontend ($lang) {

		// get Languages
		$langs = Language::all()->keyBy('code');
		$langList = $langs->map(function ($lang) {
			return $lang['name'];
		});

		// get currency list
		$currencyList = Currency::all()->keyBy('code')
           ->map(function ($currency) {
           	    return strtoupper($currency['sign']);
           });

		// get Local Settings
		$settings = LocalSettingsController::all($lang);

		return response()->json([
			'langList' => $langList,
			'lang' => $lang,
			'htmlLang' => $langs[$lang]['html_code'] ?
				$langs[$lang]['html_code'] : $lang,
			'currencyList' => $currencyList,
			'translations' => $settings,
		]);
	}

}
