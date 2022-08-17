<?php

namespace App\Http\Controllers;

use App\Country;
use App\Location;
use App\LocationTag;
use App\Tag;
use App\TagSlug;
use App\TemplateFieldValue;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;

class TemplateFieldValueController extends Controller
{


	// console command line
	private $command = null;

	private const LOG_FILE_NAME = "generation.txt";


	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index ($lang, $pageType, $field, $tagId=null) {

		$filters = [
			'page_type' => $pageType,
			'lang' => $lang,
			'field' => $field
		];

		if ( $tagId ) {
			$filters['tag_slug_id'] = TagController::getTagSlugId($tagId);
		}

		$fields = TemplateFieldValue::where($filters)->get();

		return response()->json([
			'items' => $fields,
			'tag_slug_id' => $tagId
		]);
	}


	/**
	 * Save Templates page: store new, update existing and delete what was removed
	 *
	 * @param string $lang
	 * @param string $pageType
	 * @param string $field
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function save($lang, $pageType, $field, Request $request) {
		$values = $request->input('values');

		// you can't send no fields, usually you're sending at least one empty value
		if ( !count($values) )
			return response()->json([
				'stored' => false
			]);

		// constant values
		$requiredFields = [
			'page_type' => $pageType,
			'lang' => $lang,
			'field' => $field
		];

		// if Tag - filter by tag slug ID
		$tagId = $request->input('tag_id');
		if ( $tagId )
			$requiredFields['tag_slug_id'] = TagController::getTagSlugId($tagId);

		// get existing values grouped by ID
		$oldValues = TemplateFieldValue::where($requiredFields)->get()->groupBy('id');

		// make array of existing IDs to know what to delete further
		$idsToDelete = array_keys($oldValues->toArray());

		// clean new values if they're empty
		$noEmptyValues = array_filter($values, function ($value) {
			if ( empty($value['value']) || empty( trim($value['value']) ) )
				return false;
			return true;
		});

		// store new fields
		foreach ($noEmptyValues as $value) {
			// if value already exists - update
			if ( !empty($oldValues[$value['id']]) ) {
				TemplateFieldValue::find($value['id'])->update([
					'value' => $value['value']
				]);
			}

			// if not - create
			else {
				TemplateFieldValue::create(array_merge($requiredFields, [
					'value' => $value['value']
				]));
			}

			// mark for not to delete
			if (($key = array_search($value['id'], $idsToDelete)) !== false) {
				unset($idsToDelete[$key]);
			}
		}

		// delete values
		TemplateFieldValue::whereIn('id', $idsToDelete)->delete();

		return response()->json([
			'stored' => true
		]);
	}


	/**
	 * Generate content. Preview Template fields to Pages
	 *
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\JsonResponse|void
	 */
	public function generate (Request $request) {
		$langs = $request->input('langs');
		$pageTypes = $request->input('page_types');

		if ( empty($langs) || empty($pageTypes) ) {
			return;
		}

		// start content generation
		Artisan::call(
			"generate:content",
			[
				'langs' => implode(',', $langs),
				'page_types' => implode(',', $pageTypes),
			]
		);

		return response()->json([
			'status' => 'started'
		]);
	}


	/**
	 * Run generation of the content via Artisan Command
	 * (as a background task)
	 *
	 * @param string $langs - comma separated languages
	 * @param  string $page_types - comma page types (e.g. country,location)
	 * @param null|Command $command
	 */
	public function consoleGenerate ($langs, $page_types, $command=null) {
		// enable logging to console
		if ( $command )
			$this->command = $command;

		$this->log("Command run");

		// mark as started
		GenerationStatusController::startCommon();

		// pull all template fields
		$fields = $this->getGroupedFields(
			explode(',', $langs),
			explode(',', $page_types)
		);

		// fill pages with the content
		if ( !empty($fields) )
			$this->fillPages($fields);

		$this->log("Generation start...");

		// mark as finished
		GenerationStatusController::finishCommon();

		$this->log("Finished");
	}


	/**
	 * Fill Pages with Template fields
	 *
	 * @param array $templateFields - ex.:
	 * [
	 *  'ru' => [
	 *      'country' => [
	 *          'title' => [fieldCollection1, fieldCollection2]
	 *      ]
	 *  ]
	 * ]
	 */
	private function fillPages ($templateFields) {
		foreach ($templateFields as $lang => $langGroup) {
			if ( empty($langGroup) )
				continue;
			foreach ($langGroup as $pageType => $fieldsGroups) {
				switch ($pageType) {
					case "country":
						$this->updateCountryPages($lang, $fieldsGroups);
						break;
					case "location":
						$this->updateLocationPages($lang, $fieldsGroups);
						break;
					case "poi":
						$this->updatePoiPages($lang, $fieldsGroups);
						break;
					case "tag":
						$this->updateTagPages($lang, $fieldsGroups);
						break;
					default:
						break;
				}
			}
		}
	}


	/**
	 * Fill Countries with Template Fields
	 *
	 * @param string $lang
	 * @param array $templateFields
	 */
	private function updateCountryPages ($lang, $templateFields) {
		$pages = Country::where('lang', $lang)->get();
		$this->updatePageFields($pages, $templateFields);
	}


	/**
	 * Fill Locations with Template Fields
	 *
	 * @param string $lang
	 * @param array $templateFields
	 */
	private function updateLocationPages ($lang, $templateFields) {
		$pages = Location::where([
			'lang' => $lang,
			['type', '!=', 'POI']
		])->get();
		$this->updatePageFields($pages, $templateFields);
	}


	/**
	 * Fill POIs with Template Fields
	 *
	 * @param string $lang
	 * @param array $templateFields
	 */
	private function updatePoiPages ($lang, $templateFields) {
		$pages = Location::where([
			'lang' => $lang,
			'type' => 'POI'
		])->get();
		$this->updatePageFields($pages, $templateFields);
	}


	/**
	 * Fill Tags with Template Fields
	 *
	 * @param string $lang
	 * @param array $templateFields
	 */
	private function updateTagPages ($lang, $templateFields) {
		// get grouped tag fields
		$groupedTagFields = TagController::groupTagsByTagSlugs($templateFields);

		// get all city IDs
		$locations = Location::where([
			'lang' => $lang,
			['type', '!=', 'POI']
		])->pluck('api_id')->toArray();

		// get (or create) Tags for each of this cities
		foreach ($locations as $locationId) {
			foreach ($groupedTagFields as $tagSlugId => $tagFields) {
				// get or create
				$tag = LocationTag::firstOrNew([
					'location_api_id' => $locationId,
					'tag_slug_id' => $tagSlugId,
					'lang' => $lang
				]);

				// fill with values
				foreach ($tagFields as $fieldName => $fields) {
					$randomField = $fields[rand(0, count($fields)-1)];
					$tag->$fieldName = $randomField['value'];
				}

				// save
				$tag->save();
			}
		}
	}


	/**
	 * Update given Pages with Template fields
	 *
	 * @param Collection $pages
	 * @param array $templateFields
	 */
	private function updatePageFields ($pages, $templateFields) {
		foreach ($pages as $page) {
			$changedFields = $page->changed_fields ?
				explode(',', $page->changed_fields):
				[];

			foreach ($templateFields as $fieldName => $fields) {
				if ( in_array($fieldName, $changedFields) ) {
					continue;
				}
				$randomField = $fields[rand(0, count($fields)-1)];
				$page->$fieldName = $randomField['value'];
			}

			$page->save();
		}
	}


	/**
	 * Get Template Field Values.
	 * Result format:
	 * [
	 *  'en' => [
	 *      'field_name' => [field1, field2]
	 *  ]
	 * ]
	 *
	 * @param $langs
	 * @param $pageTypes
	 *
	 * @return array
	 */
	private function getGroupedFields ($langs, $pageTypes) {
		$fields = [];
		foreach ($langs as $lang) {
			$pageTypesGroup = TemplateFieldValue::where('lang', $lang)
				->whereIn('page_type', $pageTypes)
				->get()
				->groupBy('page_type')
				->toArray();
			if ( !$pageTypesGroup || empty($pageTypesGroup) )
				continue;
			foreach ($pageTypesGroup as $pageType => $fieldsGroup) {
				foreach ($fieldsGroup as $field) {
					$fields[$lang][$pageType][$field['field']][] = $field;
				}
			}
		}
		return $fields;
	}



	/**
	 * Remove <p> tags from the string
	 *
	 * @param string $content
	 *
	 * @return string mixed
	 */
	private function removeParagraphs ($content) {
		$cleaned = str_replace('<p>', '', $content);
		$cleaned = str_replace('</p>', '', $cleaned);
		return $cleaned;
	}



	/**
	 * Log to console and file
	 *
	 * @param string $msg
	 * @param bool $error
	 * @param bool $writeFile
	 *
	 * @return null
	 */
	private function log($msg, $error=false, $writeFile=true) {
		if ( !$this->command ) return null;

		if ( $writeFile ) {
			// add date to message for logging into a file
			$logLine = date('H:i:s') . ": $msg" . PHP_EOL;
			// log into a file
			file_put_contents(
				storage_path()."/logs/".self::LOG_FILE_NAME,
				$logLine,
				FILE_APPEND
			);
		}

		// display errors in the terminal
		if ( $error ) {
			return $this->command->error( $msg );
		}

		// display alerts in the terminal
		return $this->command->info($msg);
	}


}
