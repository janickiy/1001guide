<?php

namespace App\Http\Controllers;

use App\Country;
use App\CountrySlug;
use App\LocationSlug;
use App\TemplateFieldValue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use App\Location;
use App\Http\Controllers\GetYourGuideApiController;
use Illuminate\Support\Facades\DB;
use Storage;

class LocationController extends MultilanguageController
{

	protected static $MODEL = Location::class;
	protected static $SLUG_MODEL = LocationSlug::class;
	protected const RELATION_FIELD = 'api_id';

	/**
	 * Fields that are common for Locations in every locale
	 */
	protected const COMMON_FIELDS = [
		'api_id', 'slug',
		'country_code', 'lat', 'long',
		'type', 'api_parent_id', 'bg'
	];

	private const MAX_SEARCH_ITEMS = 10;
	private const MODEL_TABLE = 'locations';
	private const SLUG_TABLE = 'location_slugs';
	private const COUNTRY_SLUG_TABLE = 'country_slugs';


	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index($parent_id=0, Request $request)
	{
		$countryCode = $request->input('country');

		$pages = static::$MODEL::where([
			'lang' => static::DEFAULT_LANG,
			'country_code' => $countryCode ?
				$countryCode :
				$this->getCountryCodeById($parent_id),
			['type', '!=', 'POI']
		])->orderBy('total_tours', 'desc')->get();

		return response()->json([
			'items' => $pages
		]);
	}


	/**
	 * Display list of POIs
	 *
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function poi(Request $request)
	{
		// filter
		$filter = [];
		$countryCode = $request->input('country', 'all');
		if ( $countryCode !== 'all' )
			$filter['country_code'] = $countryCode;
		$cityID = $request->input('city', 'all');
		if ( $cityID !== 'all' )
			$filter['api_parent_id'] = $cityID;

		// preview offset
		$offset = $request->input('offset', 0);

		// get orm
		$pages = static::$MODEL::where(
			array_merge([
				'lang' => static::DEFAULT_LANG,
				'type' => 'POI'
			], $filter)
		)->offset($offset)
		  ->limit(100)
		  ->get();

		return response()->json([
			'items' => $pages,
			'offset' => $offset
		]);
	}


	/**
	 * Destroy Location
	 *
	 * @param int $parent_id - Country ID
	 * @param int $id - Location ID
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function destroyLocation ($parent_id, $id) {
		return $this->destroy($id);
	}


	/**
	 * Location search route
	 *
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function search (Request $request) {
		$searchString = $request->input('search');
		$lang = $request->input('lang', static::DEFAULT_LANG);
		$found = $this->searchByName($searchString, $lang);
		$withUrls = $this->addUrls($found, $lang);
		return response()->json([
			'items' => $withUrls
		]);
	}


	/**
	 * Display all Location data.
	 * Merged with English if necessary
	 *
	 * @param $lang
	 * @param $slug
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function frontend ($lang, $slug, Request $request) {
		// get Location in English and requested language
		$location = $this->getTranslatedBySlug($slug, $lang);

		// get Country data
		$countryController = new CountryController();
		$country = $countryController->getTranslatedByRelationField(
			$location['country_code'], $lang
		);

		// get parent Location
		$parent = $location['type'] === 'POI' ?
			$this->getTranslatedByRelationField(
				$location['api_parent_id'], $lang
			):
			null;

		// set variables
		$currentLocation = $location['type'] === 'POI' ? $parent : $location;
		$location['variables'] = [
			'country' => $country['name'],
			'country_in' => $country['name_in_case']?
				$country['name_in_case']:
				$country['name'],
			'country_of' => $country['name_of_case']?
				$country['name_of_case']:
				$country['name'],
			'city' => $currentLocation['name'],
			'city_in' => $currentLocation['name_in_case']?
				$currentLocation['name_in_case']:
				$currentLocation['name'],
			'city_of' => $currentLocation['name_of_case']?
				$currentLocation['name_of_case']:
				$currentLocation['name'],
			'tours' => $location['total_tours'],
			'year' => date("Y")
		];
		if ( $location['type'] === 'POI' )
			$location['variables'] = array_merge($location['variables'], [
				'poi' => $location['name']
			]);

		// set keywords
		$location['keywords'] = TemplateFieldValue::where([
			'lang' => $lang,
			'page_type' => $location['type'] === 'POI' ? 'poi' : 'location',
			'field' => 'title'
		])->pluck('value')->join(', ');

		return response()->json([
			'item' => $this->replaceVariables($location),
			'country' => $country,
			'parent' => $parent,
			'nearest' => $location['type'] === 'CITY' ?
				$this->getNearestCities($location['country_code'], $location['total_tours'], $lang):
				null
		]);
	}


	/**
	 * Display Locations by country code
	 *
	 * @param string $lang
	 * @param string $country - country code
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function frontendList ($lang, $country, Request $request) {
		// get country code
		$countryCode = $this->getCountryCodeBySlug($country);

		// get Locations
		$locations = $this->getCountryLocationList($lang, $countryCode, $request->input('parent_id'));

		return response()->json([
			'items' => $this->replaceNamesWithoutTranslation($locations)
		]);
	}


	public function someLocations ($lang, Request $request) {
		$locations = DB::table(self::MODEL_TABLE)
			->select(
				self::MODEL_TABLE.".type", self::MODEL_TABLE.".name", self::MODEL_TABLE.".api_id",
				self::SLUG_TABLE.".slug",
				self::COUNTRY_SLUG_TABLE.".slug AS country_slug"
			)->leftJoin(
				self::COUNTRY_SLUG_TABLE,
				self::MODEL_TABLE.".country_code",
				"=",
				self::COUNTRY_SLUG_TABLE.".country_code"
			)->leftJoin(
				self::SLUG_TABLE,
				self::MODEL_TABLE.".api_id",
				"=",
				self::SLUG_TABLE.".api_id"
			)->where([
				"lang" => $lang
			])->whereIn(
				self::MODEL_TABLE.".api_id", $request->input("ids")
			)->get();


		return response()->json([
			'items' => $this->addUrls($locations, $lang)
		]);
	}

	/**
	 * Update Location background.
	 * Load it from the most popular Tour via GetYourGuide API
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function updateBgForAll () {
		$locations = Location::where('bg', 'like', '%tour_img%')->get();

		foreach ($locations as $location) {
			$this->addBg($this->getSlug($location));
		}
	}

	/**
	 * Add Location background if not exists yet.
	 * Load it from the most popular Tour via GetYourGuide API
	 *
	 * @param string $slug
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function addBg ($slug) {
		$location = $this->getBySlug(
			$slug, self::DEFAULT_LANG
		);

		if (isset($location[self::DEFAULT_LANG][0])) {
			$location = $location[self::DEFAULT_LANG][0];

				//return response()->json([
				//	'bg' => $location
				//]);

				// if bg already exists
				// if ( !empty($location['bg']))
				// return response()->json([
				// 	'bg' => $location['bg'],
				// ]);

			// load new bg
			$bg = $this->pullBg($location['name']);

			if ( !$bg )
				return response()->json([
					'bg' => null
				]);

			$url = $bg;
			$contents = file_get_contents($url);
			$name = $location['id'].'-'.substr($url, strrpos($url, '/') + 1);
			$upload = Storage::disk('public')->put($name, $contents);

			$image_link = null;
			if ($upload) {
				$image_link = 'https://1001guide.net/server/storage/'.$name;
			}

			// save new bg
			$locationOrm = Location::find( $location['id'] );
			$locationOrm->bg = $image_link ;
			$locationOrm->save();

			// return bg
			return response()->json([
				'bg' => $image_link,
			]);
		} else {
			return response()->json([
				'bg' => null
			]);
		}

	}


	/**
	 * Get nearest cities
	 *
	 * @param string $countryCode
	 * @param integer $currentPopularity
	 * @param string $lang
	 *
	 * @return mixed|null
	 */
	private function getNearestCities ($countryCode, $currentPopularity, $lang) {
		// get cities more popular than current
		$morePopular = $this->getCitiesSamePopularity($countryCode, $currentPopularity, $lang);
		// get cities less popular than current
		$lessPopular = $this->getCitiesSamePopularity($countryCode, $currentPopularity, $lang, false);
		// merge them
		$all = $morePopular->merge($lessPopular);
		if ( !$all->count() )
			return null;
		// return with URLs and translated names
		return $this->replaceNamesWithoutTranslation(
			$this->addUrls($all, $lang)
		);
	}


	/**
	 * Get Cities that have close number of tours
	 *
	 * @param string $countryCode
	 * @param integer $currentPopularity
	 * @param string $lang
	 * @param bool $morePopular - TRUE: look for more popular. FALSE: look for less popular
	 * @param int $limit
	 *
	 * @return \Illuminate\Support\Collection
	 */
	private function getCitiesSamePopularity (
		$countryCode, $currentPopularity, $lang, $morePopular=true, $limit=10
	) {
		return DB::table(self::MODEL_TABLE)
		         ->select(
			         self::MODEL_TABLE.".name",
			         'translations.name as local_name',
			         self::SLUG_TABLE.".slug",
			         self::COUNTRY_SLUG_TABLE.".slug as country_slug",
			         'locations.total_tours',
			         self::MODEL_TABLE.".type"
		         )
		         ->where([
			         [
						self::MODEL_TABLE.".total_tours",
						$morePopular ? '>' : '<',
						$currentPopularity
			         ],
			         [self::MODEL_TABLE.".lang", self::DEFAULT_LANG],
			         [self::MODEL_TABLE.".country_code", $countryCode],
			         [self::MODEL_TABLE.".type", 'CITY']
		         ])
		         ->leftJoin('locations as translations', function ($join) use ($lang) {
					$join->on('translations.api_id', '=', 'locations.api_id')
					     ->where('translations.lang', $lang);
		         })
		         ->leftJoin(
			         self::SLUG_TABLE,
			         self::MODEL_TABLE.".api_id",
			         '=',
			         self::SLUG_TABLE.".api_id"
		         )
		         ->leftJoin(
			         self::SLUG_TABLE.' as parents',
			         self::MODEL_TABLE.".api_parent_id",
			         '=',
			         "parents.api_id"
		         )
		         ->leftJoin(
			         self::COUNTRY_SLUG_TABLE,
			         self::MODEL_TABLE.".country_code",
			         '=',
			         self::COUNTRY_SLUG_TABLE.".country_code"
		         )
		         ->orderBy('locations.total_tours', $morePopular ? 'asc':'desc')
		         ->limit($limit)
		         ->get();
	}


	/**
	 * Get Locations in certain Country
	 *
	 * @param string $lang
	 * @param string $countryCode
	 *
	 * @return \Illuminate\Support\Collection
	 */
	private function getCountryLocationList ($lang, $countryCode, $parentId=null) {
		$whereClause = [
			'locations.lang' => self::DEFAULT_LANG,
			'locations.country_code' => $countryCode,
		];

		if ( $parentId )
			$whereClause['locations.api_parent_id'] = $parentId;
		else
			$whereClause['locations.type'] = 'CITY';

		return
			DB::table('locations')
		         ->leftJoin(
			         'location_slugs',
			         'locations.api_id', '=', 'location_slugs.api_id'
		         )
		         ->leftJoin('locations as locale', function ($join) use ($lang) {
		         	$join->on('locations.api_id', '=', 'locale.api_id')
		                 ->where('locale.lang', $lang);
		         })
		         ->where($whereClause)
		         ->whereNotNull('locations.total_tours')
		         ->select(
			         'locations.name', 'locations.id',
			         'locations.total_tours', 'locations.bg',
			         'locale.name as local_name',
			         'locale.name_in_case as name_in_case',
			         'location_slugs.slug'
		         )
		         ->orderBy('locations.total_tours', 'desc')
		         ->get();
	}


	/**
	 * Get BG Image of the most popular Tour in given location.
	 *
	 * @param string $name - Location name
	 *
	 * @return string|null
	 */
	private function pullBg ($name) {
		$bgFormatId = '145';

		$name = str_replace(' ', '%20', $name);
		$name = urlencode($name);

		// get most popular tour
		$tours = GetYourGuideApiController::request(
			"tours?q=$name",
			self::DEFAULT_LANG,
			'usd',
			1
		);

		if ( !$tours || empty($tours['data']) || empty($tours['data']['tours']) )
			return null;

		$bgTemplate = $tours['data']['tours'][0]['pictures'][0]['url'];
		if ( !$bgTemplate )
			return null;

		return str_replace('[format_id]', $bgFormatId, $bgTemplate);
	}


	/**
	 * Add URLs to Locations Collection
	 *
	 * @param \Illuminate\Support\Collection|null $locations
	 * @param string $lang
	 *
	 * @return \Illuminate\Support\Collection|null
	 */
	private function addUrls ($locations, $lang) {
		if ( !$locations || !$locations->count() )
			return null;
		foreach ($locations as &$location) {
			$location->url = $this->makeUrl(
				$location->slug, $location->country_slug, $lang,
				$location->type === 'POI' ?
					$location->parent_slug :
					null
			);
		}
		return $locations;
	}


	/**
	 * Build Location URL
	 *
	 * @param string $slug
	 * @param string $countrySlug
	 * @param string $lang
	 *
	 * @return string
	 */
	private function makeUrl ($slug, $countrySlug, $lang, $parentSlug=null) {
		if ( $parentSlug )
			return "/$lang/$countrySlug/$parentSlug/$slug";
		return "/$lang/$countrySlug/$slug";
	}


	/**
	 * Search by name
	 *
	 * @param string $name - Search string
	 * @param string $lang
	 *
	 * @return \Illuminate\Support\Collection|null
	 */
	private function searchByName ($name, $lang) {
		if ( !$name ) return null;

		// find in current language
		$locations = $this->dbSearch($name, $lang);

		if ( $locations->count() || $lang === self::DEFAULT_LANG )
			return $locations;

		// if nothing found - try English
		return $this->dbSearch($name, self::DEFAULT_LANG);
	}


	/**
	 * Search Locations with slugs and country slugs
	 *
	 * @param string $name
	 * @param string $lang
	 *
	 * @return \Illuminate\Support\Collection
	 */
	private function dbSearch ($name, $lang) {
		return DB::table(self::MODEL_TABLE)
		         ->select(
			         self::MODEL_TABLE.".name", self::MODEL_TABLE.".type",
		            self::SLUG_TABLE.".slug",
		            self::COUNTRY_SLUG_TABLE.".slug as country_slug",
			         "parents.slug as parent_slug"
		         )
		         ->where([
			         [self::MODEL_TABLE.".name", 'like', $name . '%'],
			         [self::MODEL_TABLE.".lang", $lang]
		         ])
		         ->leftJoin(
		         	self::SLUG_TABLE,
		            self::MODEL_TABLE.".api_id",
		            '=',
		            self::SLUG_TABLE.".api_id"
		         )
				->leftJoin(
					self::SLUG_TABLE.' as parents',
					self::MODEL_TABLE.".api_parent_id",
					'=',
					"parents.api_id"
				)
		         ->leftJoin(
		         	self::COUNTRY_SLUG_TABLE,
		            self::MODEL_TABLE.".country_code",
		            '=',
		            self::COUNTRY_SLUG_TABLE.".country_code"
		         )
		         ->limit(self::MAX_SEARCH_ITEMS)->get();
	}


	/**
	 * Get Slug
	 *
	 * @param Location $locationEng
	 *
	 * @return mixed
	 */
	protected function getSlug ($locationEng) {
		return $locationEng->locationSlug->slug;
	}


	/**
	 * Get Country Code by Country ID
	 *
	 * @param int|string $id
	 *
	 * @return string|null
	 */
	private function getCountryCodeById ($id) {
		$country = Country::find($id);
		if ( !$country ) return null;
		return $country->country_code;
	}


	/**
	 * Get Country Code by Country Slug
	 *
	 * @param string $slug
	 *
	 * @return string|null
	 */
	private function getCountryCodeBySlug ($slug) {
		$country = CountrySlug::where('slug', $slug)->first();
		if ( empty($country) ) return null;
		return $country->country_code;
	}


}
