<?php

namespace App\Http\Controllers;

use App\Country;
use App\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SitemapController extends Controller
{

	private const BASE_URL = "https://qwart.digital/";


	/**
	 * Sitemap Web Page
	 *
	 * @param string $lang
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function web($lang) {
		$locations = $this->getLocationsTree($lang);
		return response()->json([
			'items' => $locations
		]);
	}


	/**
	 * Generate XML sitemaps
	 *
	 * @return string
	 */
	public function xml () {
		$langs = LanguageController::codesArray();
		$commonXml = '';

		foreach ($langs as $lang) {
			$xmlForLang = $this->getXmlForLang( $lang );
			$this->saveXml($xmlForLang, $lang);
			$commonXml .= $xmlForLang;
		}

		$this->saveXml($commonXml);

		return 'done!';
	}


	/**
	 * Save XML to file(s)
	 *
	 * @param string $xml
	 * @param null|string $lang
	 */
	private function saveXml ($xml, $lang=null) {
		// make XML to save
		$xmlToSave = '<?xml version="1.0" encoding="UTF-8"?>';
		$xmlToSave .= '<urlset xmlns="https://www.sitemaps.org/schemas/sitemap/0.9"
			xmlns:xsi="https://www.w3.org/2001/XMLSchema-instance"
			xsi:schemaLocation="https://www.sitemaps.org/schemas/sitemap/0.9
			https://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';
		$xmlToSave .= $xml;
		$xmlToSave .= '</urlset>';

		// make filename
		$filename = public_path() . "/" . ($lang ? "sitemap_$lang.xml" : "sitemap.xml");

		// save a new one
		file_put_contents($filename, $xmlToSave);
	}


	/**
	 * Generate XML sitemap string for Language
	 *
	 * @param string $lang
	 *
	 * @return string
	 */
	private function getXmlForLang ($lang) {
		// we'll store XML here
		$xmlString = '';

		// home page
		$homePageUrl = self::BASE_URL . "$lang";
		$xmlString .= "<url><loc>$homePageUrl</loc></url>";

		// sitemap URL
		$siteMapUrl = "$homePageUrl/ratings";
		$xmlString .= "<url><loc>$siteMapUrl</loc></url>";

		// get location tree
		$locations = $this->getLocationsTree($lang, true);

		foreach ($locations as $country) {
			// make XML for country
			$countryUrl = "$homePageUrl/" . $country->slug;
			$xmlString .= "<url><loc>$countryUrl</loc></url>";

			// go throw cities
			if ( empty($country->items) ) continue;
			foreach ($country->items as $cities) {
				// make XML for cities
				$city = $cities[0];
				if (empty($city->name)) continue;
				$cityUrl = "$countryUrl/" . $city->slug;
				$xmlString .= "<url><loc>$cityUrl</loc></url>";

				// make XML for tags
				if ( empty($city->tags) ) continue;
				foreach ($city->tags as $tag) {
					if (empty($tag->slug)) continue;
					$tagUrl = "$cityUrl/tag/" . $tag->slug;
					$xmlString .= "<url><loc>$tagUrl</loc></url>";
				}

				// make XML for POIs
				if ( empty($city->items) ) continue;
				foreach ($city->items as $poi) {
					if (empty($poi->name)) continue;
					$poiUrl = "$cityUrl/" . $poi->slug;
					$xmlString .= "<url><loc>$poiUrl</loc></url>";
				}
			}
		}

		return $xmlString;
	}


	/**
	 * Get Locations tree: Country => Cities/Areas => POIs
	 *
	 * @param string $lang
	 * @param boolean $getTags
	 *
	 * @return \Illuminate\Support\Collection
	 */
	private function getLocationsTree ($lang, $getTags=false) {

		// get all countries
		$countries = DB::table('countries')
           ->leftJoin('countries as translations', function ($join) use ($lang) {
               $join->on('translations.country_code', '=', 'countries.country_code')
                    ->where('translations.lang', $lang);
           })
			// with slugs
           ->leftJoin(
               'country_slugs as slugs',
               'countries.country_code', '=', 'slugs.country_code'
           )
           ->where([
               'countries.lang' => 'en'
           ])
           ->select(
               'countries.name', 'countries.country_code', 'slugs.slug',
               'translations.name as local_name'
           )
           ->orderBy('countries.name', 'asc')
           ->get();

		// go throw the countries
		foreach ($countries as &$country) {

			// replace names
			if ( $country->local_name )
				$country->name = $country->local_name;

			// get all locations
			$locations = DB::table('locations')
               ->join(
	               'location_slugs as slugs',
	               'locations.api_id', '=', 'slugs.api_id'
               )
               ->where([
	               'locations.lang' => $lang,
	               'locations.country_code' => $country->country_code
               ])
               ->select(
	               'locations.name', 'locations.api_id',
	               'locations.api_parent_id', 'slugs.slug',
	               'locations.type'
               )
	           ->orderBy('locations.name', 'asc')
               ->get()->groupBy('api_id')->toArray();

			// combine POIs
			foreach ($locations as $api_id => $location) {
				$currentLocation = $location[0];
				if ( $currentLocation->type !== 'POI' ) continue;
				$locations[$currentLocation->api_parent_id][0]->items[] = $currentLocation;
				unset($locations[$api_id]);
			}

			// pull tags for cities
			if ( $getTags ) {
				foreach ($locations as $location) {
					$currentLocation = $location[0];
					if ( empty($currentLocation->api_id) ) continue;
					$currentLocation->tags = $this->getTags($currentLocation->api_id);
				}
			}

			$country->items = $locations;
		}

		return $countries;
	}


	/**
	 * Get Tag Slugs for Location
	 *
	 * @param integer $locationApiId
	 *
	 * @return \Illuminate\Support\Collection
	 */
	private function getTags ($locationApiId) {
		return DB::table('location_tags')
			->leftJoin(
				'tag_slugs',
				'tag_slugs.id', '=', 'location_tags.tag_slug_id'
			)
			->where([
				'location_api_id' => $locationApiId,
				'lang' => 'en',
				['total_tours', '!=', 0]
			])
			->select('tag_slugs.slug')
			->get();
	}


}
