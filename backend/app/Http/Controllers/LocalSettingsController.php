<?php

namespace App\Http\Controllers;

use App\LocalSetting;
use App\LocalSettingTranslation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocalSettingsController extends Controller
{

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		$settings = LocalSetting::all();
		$translation = LocalSettingTranslation::where('lang', $request->input('lang'))
		                                      ->get()->keyBy('setting_id');

		// add values
		foreach ($settings as &$setting) {
			$setting->value = ($translation && !empty($translation[$setting->id])) ?
				$translation[$setting->id]->value :
				'';
		}

		return response()->json([
			'success' => true,
			'items' => $settings,
			'lang' => $translation
		]);
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		$lang = $request->input('lang');
		if ( !$lang )
			return null;

		$for_update = array();
		foreach ($request->all() as $field => $value) {

			// check for setting exists
			$setting = LocalSetting::where('name', $field)->first();
			if ( !$setting )
				continue;

			// get translation
			$translation = LocalSettingTranslation
				::where('setting_id', $setting->id)
				->where('lang', $lang)
				->first();

			if ( $translation )
				$translation->update(['value' => $value]);
			else {
				LocalSettingTranslation::create([
					'setting_id' => $setting->id,
					'lang' => $lang,
					'value' => $value
				]);
			}

			$for_update[$field] = $value;
		}

		return response()->json([
			'updated' => $for_update,
		]);
	}


	/**
	 * Get settings list
	 *
	 * @param string $lang
	 *
	 * @return array
	 */
	public static function all ($lang) {
		$settings = DB::table('local_settings')
			// get English
			->leftJoin('local_setting_translations as defaults', function ($join) {
				$join->on('local_settings.id', '=', 'defaults.setting_id')
				     ->where('defaults.lang', 'en');
			})
			// get Local
			->leftJoin('local_setting_translations as local', function ($join) use ($lang) {
				$join->on('local_settings.id', '=', 'local.setting_id')
				     ->where('local.lang', $lang);
			})
			->select(
				'local_settings.name',
				'defaults.value as default_value',
				'local.value'
			)
			->get();
		return self::pluck($settings);
	}


	/**
	 * Make [name => value] array from Settings Collection
	 *
	 * @param \Illuminate\Support\Collection $settings
	 *
	 * @return array array
	 */
	private static function pluck ($settings) {
		$settingsArray = [];
		foreach ($settings as $setting) {
			$settingsArray[$setting->name] = $setting->value ?
				$setting->value : $setting->default_value;
		}
		return $settingsArray;
	}

}
