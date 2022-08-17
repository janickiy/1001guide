<?php

namespace App\Http\Controllers;

use App\Country;
use App\CountrySlug;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class MultilanguageController extends Controller
{

	protected static $MODEL;
	protected static $SLUG_MODEL;
	protected const RELATION_FIELD = 'country_code';

	/**
	 * Fields that are common for Country in every locale
	 */
	protected const COMMON_FIELDS = [];
	protected const NON_TRANSLATABLE = ['name_in_case'];

	/**
	 * Fields that may contain variables inside
	 */
	protected const FIELDS_WITH_VARIABLES = [
		'title', 'announce', 'title_bottom', 'content', 'meta_description', 'keywords'
	];

	protected const DEFAULT_LANG = 'en';



	/**
	 * Display all Country data.
	 * Merged with English if necessary
	 *
	 * @param $lang
	 * @param $slug
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function frontend ($lang, $slug, Request $request) {
		// get Location in English and requested language
		$location = $this->getTranslatedBySlug($slug, $lang);

		return response()->json([
			'item' => $location
		]);
	}


	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index($parent_id=0, Request $request)
	{
		$pages = static::$MODEL::where('lang', static::DEFAULT_LANG);

		$orderBy = $request->input('order');
		if ( $orderBy )
			$pages->orderBy($orderBy, 'asc');

		$pages = $pages->get();

		return response()->json([
			'items' => $pages
		]);

	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @param Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function show($id, Request $request)
	{
		$lang = $request->input('lang', static::DEFAULT_LANG);

		$country = $this->getWithCommonFields($id, $lang);

		return response()->json([
			'item' => $country ? $country : array(),
			'lang' => $lang
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
		$lang = $request->input('lang', static::DEFAULT_LANG);

		// save current translation
		$saved = static::$MODEL::create($request->all());

		// duplicate translation to English
		if ( $lang !== static::DEFAULT_LANG )
			static::$MODEL::create( $request->merge(['lang' => 'en'])->all() );

		// add a slug
		$this->updateSlug(
			$request->input(static::RELATION_FIELD),
			$request->input('slug') ?:
				SlugController::slugFromName($request->input('name'))
		);

		// response
		return response()->json([
			'stored' => true,
			'id' => $saved->id
		]);
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
		$lang = $request->input('lang', static::DEFAULT_LANG);

		// try to get the country translation
		$country = $this->getByEngId($id, $lang);

		// update common fields first
		$commonUpdated = $this->updateCommonFields($id, $country, $request);

		// if we don't have a translation yet - then create it
		if ( !$country ) {
			static::$MODEL::create($request->all());
		}
		// otherwise, update
		else {
			$country->update($request->all());
		}

		// update slug
		$this->updateSlug($request->input(static::RELATION_FIELD), $request->input('slug'));

		// response
		return response()->json([
			'updated' => true,
			'success' => 'Элемент обновлён',
			'common' => $commonUpdated
		]);
	}


	protected function updateCommonFields ($id, $currentModel, $request) {
		// get common field for all the translations
		$relationFieldValue = $this->getRelationFieldValueById($id);

		// collect all fields to update
		$fieldsToUpdate = [];
		foreach (static::COMMON_FIELDS as $fieldName) {
			// skip the Slug, bc it is in another table
			if ( $fieldName === 'slug' ) continue;

			// skip fields that absent in Request
			if ( !$request->has($fieldName) ) continue;

			// skip unchanged fields
			$newFieldValue = $request->get($fieldName);
			if ( $currentModel && $currentModel->$fieldName === $newFieldValue )
				continue;

			// add field for updating
			$fieldsToUpdate[$fieldName] = $newFieldValue;
		}

		// update all translations
		static::$MODEL::where([
			static::RELATION_FIELD => $relationFieldValue
		])->update($fieldsToUpdate);

		return $fieldsToUpdate;
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		// get all Country translations
		$countryCode = $this->getRelationFieldValueById($id);
		static::$MODEL::where(static::RELATION_FIELD, $countryCode)->delete();

		// delete slug
		static::$SLUG_MODEL::where(static::RELATION_FIELD, $countryCode)->delete();

		return response()->json([
			'deleted' => true,
			'success' => 'Элемент удалён'
		]);
	}


	/**
	 * Multiple destroying
	 *
	 * @param Request $request
	 *
	 * @return bool|\Illuminate\Http\JsonResponse
	 */
	public function destroyMultiple(Request $request) {
		$ids = $request->input('ids');
		if ( !$ids ) return false;
		foreach ( explode(',', $ids) as $id ) {
			$this->destroy( (int)$id );
		}
		return response()->json([
			'deleted_all' => true,
			'success' => 'Элементы удалены',
			'ids' => $request->input('ids')
		]);
	}


	/**
	 * Get Country Model in current language.
	 * Attach common fields which are the same for any translation.
	 * (We're getting them from the Model on DEFAULT_LANG)
	 *
	 * @param int $id
	 * @param string $lang
	 *
	 * @return Country|array|null
	 */
	private function getWithCommonFields ($id, $lang) {

		// get English version of the Country
		$countryEng = static::$MODEL::find($id);
		$countryEng->slug = $this->getSlug($countryEng);

		// if we need English locale of the item - return it without any changes by ID
		if ( $lang === static::DEFAULT_LANG )
			return $countryEng;

		// for any other translations
		$country = $this->getByEngId($id, $lang);

		// if Country translation doesn't exist yet -
		// return an array with some fields from English version
		if ( !$country ) {
			$countryArray = [];
			foreach (static::COMMON_FIELDS as $field) {
				$countryArray[$field] = $countryEng->$field;
			}
			return $countryArray;
		}

		// if we already have a translation
		foreach (static::COMMON_FIELDS as $field) {
			if ( !$country->$field )
				$country->$field = $countryEng->$field;
		}
		return $country;

	}


	/**
	 * Get Slug
	 *
	 * @param Country $countryEng
	 *
	 * @return mixed
	 */
	protected function getSlug ($countryEng) {
		return $countryEng->countrySlug->slug;
	}


	/**
	 * Update Slug
	 *
	 * @param string $countryCode
	 * @param string $slug
	 *
	 * @return CountrySlug
	 */
	protected function updateSlug ($countryCode, $slug) {
		$countrySlug = static::$SLUG_MODEL::where(static::RELATION_FIELD, $countryCode)->first();
		if ( $countrySlug )
			$countrySlug->update(['slug' => $slug]);
		else
			static::$SLUG_MODEL::create([
				static::RELATION_FIELD => $countryCode,
				'slug' => $slug
			]);
	}


	/**
	 * Get Country by it's English translation ID
	 *
	 * @param string $id - ID of the Country in English
	 * @param string $lang - language of Country we need
	 *
	 * @return Country|null
	 */
	protected function getByEngId ($id, $lang) {
		return static::$MODEL::where([
			'lang' => $lang,
			static::RELATION_FIELD => $this->getRelationFieldValueById($id)
		])->first();
	}


	/**
	 * Get Country code by Country ID
	 *
	 * @param int $id
	 *
	 * @return null|string
	 */
	private function getRelationFieldValueById ($id) {
		$country = static::$MODEL::find($id);
		if ( !$country ) return null;
		return $country->{static::RELATION_FIELD};
	}


	/**
	 * Get relation field by Slug
	 *
	 * @param string $slug
	 *
	 * @return string|null
	 */
	private function getRelationFieldValueBySlug ($slug) {
		$slug = static::$SLUG_MODEL::where('slug', $slug)->first();
		if ( !$slug ) return null;
		return $slug->{static::RELATION_FIELD};
	}


	/**
	 * Get Model by slug.
	 * Replace empty field by English translation.
	 *
	 * @param string $slug
	 * @param string $lang
	 *
	 * @return array
	 */
	protected function getTranslatedBySlug($slug, $lang) {
		$translations = $this->getBySlug($slug, $lang);
		return $this->mergeLocales($translations, $lang);
	}


	/**
	 * Get translated item by its RELATION_FIELD
	 *
	 * @param string|integer $relationFieldValue
	 * @param string $lang
	 *
	 * @return array
	 */
	public function getTranslatedByRelationField($relationFieldValue, $lang) {
		$items = static::$MODEL::where(static::RELATION_FIELD, $relationFieldValue)
		                ->whereIn('lang', [$lang, self::DEFAULT_LANG])
		                ->get()->groupBy('lang')->toArray();
		return $this->mergeLocales($items, $lang);
	}


	/**
	 * Get Elements by Slug.
	 * Get English version + localized with $lang
	 *
	 * @param string $slug
	 * @param string $lang
	 *
	 * @return Collection
	 */
	protected function getBySlug ($slug, $lang) {
		$relationFieldValue = $this->getRelationFieldValueBySlug($slug);
		$items = static::$MODEL::where(static::RELATION_FIELD, $relationFieldValue)
			->whereIn('lang', [$lang, self::DEFAULT_LANG])
			->get()->groupBy('lang')->toArray();
		return $items;
	}


	/**
	 * Merge translations.
	 * Replace empty field by English translation.
	 *
	 * @param array $modelsGroupedByLang - e.g. ['en' => [...], 'ru' => [...]]
	 * @param string $lang
	 *
	 * @return array - [...] (w/o locale index)
	 */
	//protected function mergeLocales ($modelsGroupedByLang, $lang) {
	//	$defaultModel = $modelsGroupedByLang[self::DEFAULT_LANG];
	//
	//	// return English version if we need the one or if we haven't requested translation
	//	if ( $lang === self::DEFAULT_LANG || empty($modelsGroupedByLang[$lang]) )
	//		return $defaultModel;
	//
	//	$localedModel = $modelsGroupedByLang[$lang];
	//	$merged = [];
	//
	//	// merge translations
	//	foreach ($localedModel as $fieldName => $value) {
	//		// except non translatable fields
	//		$merged[$fieldName] =
	//			($value || in_array($fieldName, self::NON_TRANSLATABLE)) ?
	//			$value : $defaultModel[$fieldName];
	//	}
	//
	//	return $merged;
	//}


	protected function mergeLocales ($modelsGroupedByLang, $lang) {
		$defaultModel = $modelsGroupedByLang[self::DEFAULT_LANG][0];

		// return English version if we need the one or if we haven't requested translation
		if ( $lang === self::DEFAULT_LANG || empty($modelsGroupedByLang[$lang]) )
			return $defaultModel;

		$localedModel = $modelsGroupedByLang[$lang][0];
		$merged = [];

		//var_dump($defaultModel);
		//var_dump($localedModel);

		// merge translations
		foreach ($localedModel as $fieldName => $value) {
			// except non translatable fields
			$shouldUseLocaledValue = $value || in_array(
				$fieldName, self::NON_TRANSLATABLE
			);
			$merged[$fieldName] =
				$shouldUseLocaledValue ?
					$value : $defaultModel[$fieldName];
		}

		return $merged;
	}



	protected function replaceVariables (&$page) {
		foreach (self::FIELDS_WITH_VARIABLES as $field) {
			if ( empty($page[$field]) ) continue;
			foreach ($page['variables'] as $variableName => $value) {
				$page[$field] = str_replace("{".$variableName."}", $value, $page[$field]);
			}
		}
		return $page;
	}


	/**
	 * Replace a "name" property with translation
	 *
	 * @param array|\Illuminate\Support\Collection $locations
	 *
	 * @return mixed
	 */
	protected function replaceNamesWithoutTranslation ($locations) {
		foreach ($locations as &$location) {
			if ( $location->local_name )
				$location->name = $location->local_name;
		}
		return $locations;
	}

}
