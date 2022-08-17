<?php

namespace App\Http\Controllers;

use App\Location;
use App\LocationTag;
use App\Tag;
use App\TagSlug;
use App\TemplateFieldValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TagController extends MultilanguageController
{
    protected static $MODEL = Tag::class;
    protected static $SLUG_MODEL = TagSlug::class;
    protected const RELATION_FIELD = 'get_your_guide_id';


	/**
	 * Fields that are common in every locale
	 */
	protected const COMMON_FIELDS = [
		'get_your_guide_id', 'slug'
	];


	// console command line
	private $command = null;


	/**
	 * Display Tag data
	 *
	 * @param string $lang
	 * @param string $slug
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function frontend ($lang, $slug, Request $request) {
		// get Tag in English and requested language
		$tag = $this->getTranslatedBySlug($slug, $lang);

		// get Location and Country data
		$locationController = new LocationController();
		$location = $locationController->frontend(
			$lang, $request->input('city'), $request
		)->original;
		$city = $location['item'];
		$country = $location['country'];

		// get Tag Location data
		$tagSlugId = TagController::getTagSlugId($tag['id']);
		$locationTag = LocationTag::where([
			'lang' => $lang,
			'location_api_id' => $city['api_id'],
			'tag_slug_id' => $tagSlugId
		])->first(array_diff(self::FIELDS_WITH_VARIABLES, ['keywords']));
		if ( $locationTag )
			$tag = array_merge($tag, $locationTag->toArray());

		// set keywords
		$tag['keywords'] = TemplateFieldValue::where([
			'lang' => $lang,
			'page_type' => 'tag',
			'field' => 'title',
			'tag_slug_id' => $tagSlugId
		])->pluck('value')->join(', ');


		// set variables
		$tag['variables'] = [
			'country' => $country['name'],
			'country_in' => $country['name_in_case']?
				$country['name_in_case']:
				$country['name'],
			'country_of' => $country['name_of_case']?
				$country['name_of_case']:
				$country['name'],
			'city' => $city['name'],
			'city_in' => $city['name_in_case']?
				$city['name_in_case']:
				$city['name'],
			'city_of' => $city['name_of_case']?
				$city['name_of_case']:
				$city['name'],
			'tours' => $city['total_tours'],
			'year' => date("Y"),
			'tag' => $tag['name']
		];

		// json
		return response()->json([
			'item' => $this->replaceVariables($tag),
			'location' => $city,
			'country' => $country,
		]);
	}


	/**
	 * JSON list of all tags with names and slugs
	 *
	 * @param string $lang
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function list ($lang, Request $request) {
		// set limit
		$limit = $request->input('limit', 20);

		// get location API ID
		$locationApiId = $request->input('location_api_id');

		// get tags
		$tags = DB::table('tags')
			->leftJoin('tags as locale', function ($join) use ($lang) {
				$join->on('tags.get_your_guide_id', '=', 'locale.get_your_guide_id')
				     ->where('locale.lang', $lang);
			})
			->leftJoin(
				'tag_slugs as slugs',
				'tags.get_your_guide_id', '=', 'slugs.get_your_guide_id'
			)
			// exclude tags without any tours in current location
			->join('location_tags', function ($join) use ($locationApiId) {
				$join->on('slugs.id', '=', 'location_tags.tag_slug_id')
				     ->where([
					     'location_tags.lang' => 'en',
					     'location_tags.location_api_id' => $locationApiId,
					     ['location_tags.total_tours', '!=', 0]
				     ]);
			})
			->where([
				'tags.lang' => self::DEFAULT_LANG
			])
			->select(
				'tags.name as default_name', 'locale.name', 'slugs.slug',
				'tags.get_your_guide_id as id', 'location_tags.total_tours'
			)
			->limit($limit)
			->get();

		// merge locales
		$tagsLocalized = $tags->map(function ($tag) {
			return [
				'slug' => $tag->slug,
				'name' => $tag->name ?: $tag->default_name,
				'id' => $tag->id,
				'total_tours' => $tag->total_tours
			];
		});

		return response()->json([
			'items' => $tagsLocalized,
		]);
	}


	/**
	 * Update Total Tours count in Tags
	 *
	 * @param null $command
	 */
	public function updateTotals ($command=null) {
		// enable logging to console
		if ( $command )
			$this->command = $command;

		// get all locations
		$locations = Location::where([
			'lang' => 'en',
			['type', '!=', 'POI'],
		])->get(['name', 'api_id']);

		// get all tag types
		$tags = TagSlug::all(['get_your_guide_id', 'id']);

		foreach ($locations as $location) {
			$this->log("Location is: " . $location->name);
			foreach ($tags as $tag) {
				$this->log("Tag GYG ID: " . $tag->get_your_guide_id);
				$locationTag = LocationTag::firstOrNew([
					'lang' => self::DEFAULT_LANG,
					'location_api_id' => $location->api_id,
					'tag_slug_id' => $tag->id
				]);
				if ( !$locationTag->total_tours && $locationTag->total_tours !== 0 ) {
					$this->log("Location totals need to be updated");
					$tours = GetYourGuideApiController::request(
						"tours?q=" . urlencode($location->name) .
									"&categories[]=" . $tag->get_your_guide_id,
						"en",
						"USD",
						1
					);
					$total = $tours["_metadata"]["totalCount"];
					$this->log("Total: $total");
					$locationTag->total_tours = $total;
					$locationTag->save();
					sleep(1);
				}
				else {
					$this->log("No need to update totals: " . $locationTag->total_tours);
				}
			}
		}

		$this->log("Finished!");
	}


	/**
	 * Get the ID of Tag's Slug
	 *
	 * @param integer $tagId
	 *
	 * @return mixed
	 */
	public static function getTagSlugId ($tagId) {
		$tag = Tag::find($tagId);
		$tagSlug = TagSlug::where([
			self::RELATION_FIELD => $tag->{self::RELATION_FIELD}
		])->first();
		return $tagSlug->id;
	}


	/**
	 * Group Tags Template fields by Tag Slug ID
	 *
	 * @param $tagsArray
	 *
	 * @return array
	 */
	public static function groupTagsByTagSlugs ($tagsArray) {
		$tagsSorted = [];
		foreach ($tagsArray as $fieldName => $tags) {
			foreach ($tags as $tag) {
				if ( !$tag['tag_slug_id'] ) continue;
				$tagsSorted[ $tag['tag_slug_id'] ][ $fieldName ][] = $tag;
			}
		}
		return$tagsSorted;
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
