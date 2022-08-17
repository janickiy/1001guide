<?php

namespace App\Http\Controllers;

use App\Country;
use App\CountrySlug;
use App\Location;
use Illuminate\Http\Request;

class CountryParserController extends Controller
{

    // API for pulling country name by its code
    private const API_URL = "https://restcountries.eu/rest/v2/alpha/";

    // default language
    private const LANG = 'en';


    /**
     * Make slugs for all the countries
     */
    public function makeSlugs () {

        // get all the countries
        $countries = Country::where('lang', 'en')->get();

        foreach ($countries as $country) {
            CountrySlug::firstOrCreate([
                'country_code' => $country['country_code'],
                'slug' => SlugController::slugFromName(
                    $country->name
                )
            ]);
        }

    }


    /**
     * Add Countries via some API
     */
    public function parseAll () {

        // get all possible locations' country codes
        $locationCountries = Location::where('lang', 'en')->get(['country_code']);

        foreach ($locationCountries as $locationCountry) {

            $countryCode = strtolower($locationCountry->country_code);

            // check if country already exists
            $countryExists = Country::where('country_code', $countryCode)->exists();
            if ( $countryExists ) continue;

            // get country by API
            $countryFromApi = $this->getViaAPi($countryCode);

            // add country
            $this->addCountry($countryCode, $countryFromApi['name'], self::LANG);

        }

    }



    public function duplicateCountries () {
    	// gel all languages
    	$langs = LanguageController::codesArray();

    	// get all Countries in English
    	$countriesEng = Country::where([
    		'lang' => 'en'
	    ])->get();

    	// duplicate Country to another languages if doesn't exist yet
		foreach ($countriesEng as $countryEng) {
			$countryCode = $countryEng->country_code;

			foreach ($langs as $lang) {
				// skip English
				if ( $lang === "en" ) continue;

				$countryTranslation = Country::where([
					'lang' => $lang,
					'country_code' => $countryCode
				])->first();

				// skip existing
				if ( $countryTranslation )
					continue;

				// save new
				Country::create([
					'lang' => $lang,
					'name' => $countryEng->name,
					'country_code' => $countryCode,
					'total' => $countryEng->total
				]);
			}
		}
    }


    /**
     * Save the Country
     *
     * @param string $countryCode
     * @param string $name
     * @param string $lang
     */
    private function addCountry ($countryCode, $name, $lang) {
        Country::firstOrCreate([
            'country_code' => $countryCode,
            'name' => $name,
            'lang' => $lang
        ]);
    }


    /**
     * Get Country from API
     *
     * @param string $countryCode
     *
     * @return array|null
     */
    private function getViaAPi ($countryCode) {
        $response = file_get_contents(self::API_URL . $countryCode);
        $country = json_decode($response, true);
        return $country;
    }


}
