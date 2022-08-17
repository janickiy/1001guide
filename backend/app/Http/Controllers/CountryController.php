<?php

namespace App\Http\Controllers;

use App\Country;
use App\CountrySlug;
use App\Location;
use App\TemplateFieldValue;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CountryController extends MultilanguageController
{

	protected static $MODEL = Country::class;
	protected static $SLUG_MODEL = CountrySlug::class;
	protected const RELATION_FIELD = 'country_code';

	/**
	 * Fields that are common for Country in every locale
	 */
	protected const COMMON_FIELDS = [
		'country_code', 'slug'
	];

	// console command line
	private $command = null;


	/**
	 * Show all Country information to the Front
	 *
	 * @param $lang
	 * @param $slug
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function frontend ($lang, $slug, Request $request) {
		// get Location in English and requested language
		$country = $this->getTranslatedBySlug($slug, $lang);

		// get the most popular location
		$topLocation = $this->topLocation($country['country_code']);
		//$country['top'] = $topLocation;

		// add bg
		if ( $topLocation )
			$country['bg'] = $topLocation->bg;

		// set variables
		$country['variables'] = [
			'country' => $country['name'],
			'country_in' => $country['name_in_case']?
				$country['name_in_case']:
				$country['name'],
			'country_of' => $country['name_of_case']?
				$country['name_of_case']:
				$country['name'],
			'tours' => $country['total'],
			'year' => date("Y")
		];

		// set keywords
		$country['keywords'] = TemplateFieldValue::where([
			'lang' => $lang,
			'page_type' => 'country',
			'field' => 'title'
		])->pluck('value')->join(', ');

		return response()->json([
			'item' => $this->replaceVariables($country),
			'nearest' => $this->getNearestCountries($lang)
		]);
	}


	/**
	 * Get Countries
	 *
	 * @param int $parent_id - unused argument
	 * @param Request $request - unused too
	 *
	 * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
	 */
	public function index($parent_id=0, Request $request)
	{
		$lang = static::DEFAULT_LANG;

		$pages = DB::table('countries')
			->leftJoin('locations', function ($join) use ($lang) {
				$join->on('locations.country_code', '=', 'countries.country_code')
				     ->where('locations.lang', $lang);
			})
			->groupBy('countries.id')
			->where('countries.lang', $lang)
			->select(
				'countries.*',
				DB::raw('SUM(locations.total_tours) AS total_tours')
			)
			->orderBy('total_tours', 'desc')
			->get();

		return response()->json([
			'items' => $pages
		]);
	}


	/**
	 * Update totals via Command Line
	 *
	 * @param null|Command $command
	 */
	public function updateTotals ($command=null) {
		// enable logging to console
		if ( $command )
			$this->command = $command;

		// get all countries in English
		$countries = Country::where('lang', self::DEFAULT_LANG)->get();
		foreach ($countries as $country) {
			$countryCode = $country['country_code'];

			// find totals of Locations inside
			$total = DB::table('locations')->where([
				'lang' => self::DEFAULT_LANG,
				'country_code' => $countryCode
			])->sum('total_tours');

			// log
			$this->log($country->name . ": " . $total);

			// save totals for the Country in all languages
			$countryTranslations = Country::where('country_code', $countryCode)->get();
			foreach ($countryTranslations as $translation) {
				$translation->total = $total;
				$translation->save();
			}
		}
	}



	/**
	 * Get Top Location of Country
	 *
	 * @param $countryCode
	 *
	 * @return mixed
	 */
	private function topLocation ($countryCode) {
		return Location::where([
			'lang' => self::DEFAULT_LANG,
			'country_code' => $countryCode
		])->whereNotNull('bg')
		  ->orderBy('total_tours', 'desc')
		  ->first();
	}


	/**
	 * Get Countries nearby
	 *
	 * @param string $lang
	 * @param int $limit
	 *
	 * @return Collection
	 */
	private function getNearestCountries ($lang, $limit=20) {
		$countries = DB::table('countries')
		               ->leftJoin('countries as translations', function ($join) use ($lang) {
			               $join->on('translations.country_code', '=', 'countries.country_code')
			                    ->where('translations.lang', $lang);
		               })
		               ->leftJoin(
			               'country_slugs as slugs',
			               'countries.country_code', '=', 'slugs.country_code'
		               )
		               ->where([
			               'countries.lang' => self::DEFAULT_LANG
		               ])
		               ->select(
			               'countries.name', 'slugs.slug', 'translations.name as local_name'
		               )
		               ->inRandomOrder()
		               ->limit($limit)
		               ->get();
		return $this->replaceNamesWithoutTranslation(
			$this->addUrls($countries, $lang)
		);
	}


	/**
	 * Add URLs to Country array
	 *
	 * @param array|Collection $countries
	 * @param string $lang
	 *
	 * @return Collection|array|null
	 */
	private function addUrls ($countries, $lang) {
		if ( !$countries || !$countries->count() )
			return null;
		foreach ($countries as &$country) {
			$country->url = "/$lang/".$country->slug;
		}
		return $countries;
	}


	/**
	 * Log to console and file
	 *
	 * @param string $msg
	 * @param bool $error
	 *
	 * @return null
	 */
	private function log($msg, $error=false) {
		if ( !$this->command ) return null;

		// display errors in the terminal
		if ( $error ) {
			return $this->command->error( $msg );
		}

		// display alerts in the terminal
		return $this->command->info($msg);
	}

}
