<?php

namespace App\Http\Controllers;

use App\LocationSlug;
use Illuminate\Http\Request;
use App\Location;
use App\Http\Controllers\GetYourGuideApiController;
use Illuminate\Console\Command;

class LocationParserController extends Controller
{

	// console command line
	private $command = null;

	private const LOG_FILE_NAME = "locations_parse.txt";

	// list of existing countries
	private const COUNTRY_LIST = [
		'Afghanistan',
		'Albania',
		'Algeria',
		'American Samoa',
		'Andorra',
		'Angola',
		'Anguilla',
		'Antarctica',
		'Antigua and Barbuda',
		'Argentina',
		'Armenia',
		'Aruba',
		'Australia',
		'Austria',
		'Azerbaijan',
		'Bahamas',
		'Bahrain',
		'Bangladesh',
		'Barbados',
		'Belarus',
		'Belgium',
		'Belize',
		'Benin',
		'Bermuda',
		'Bhutan',
		'Bolivia',
		'Bosnia and Herzegowina',
		'Botswana',
		'Bouvet Island',
		'Brazil',
		'British Indian Ocean Territory',
		'Brunei Darussalam',
		'Bulgaria',
		'Burkina Faso',
		'Burundi',
		'Cambodia',
		'Cameroon',
		'Canada',
		'Cape Verde',
		'Cayman Islands',
		'Central African Republic',
		'Chad',
		'Chile',
		'China',
		'Christmas Island',
		'Cocos (Keeling) Islands',
		'Colombia',
		'Comoros',
		'Congo',
		'Congo, the Democratic Republic of the',
		'Cook Islands',
		'Costa Rica',
		'Cote d\'Ivoire',
		'Croatia (Hrvatska)',
		'Cuba',
		'Cyprus',
		'Czech Republic',
		'Denmark',
		'Djibouti',
		'Dominica',
		'Dominican Republic',
		'East Timor',
		'Ecuador',
		'Egypt',
		'El Salvador',
		'Equatorial Guinea',
		'Eritrea',
		'Estonia',
		'Ethiopia',
		'Falkland Islands (Malvinas)',
		'Faroe Islands',
		'Fiji',
		'Finland',
		'France',
		'France Metropolitan',
		'French Guiana',
		'French Polynesia',
		'French Southern Territories',
		'Gabon',
		'Gambia',
		'Georgia',
		'Germany',
		'Ghana',
		'Gibraltar',
		'Greece',
		'Greenland',
		'Grenada',
		'Guadeloupe',
		'Guam',
		'Guatemala',
		'Guinea',
		'Guinea-Bissau',
		'Guyana',
		'Haiti',
		'Heard and Mc Donald Islands',
		'Holy See (Vatican City State)',
		'Honduras',
		'Hong Kong',
		'Hungary',
		'Iceland',
		'India',
		'Indonesia',
		'Iran (Islamic Republic of)',
		'Iraq',
		'Ireland',
		'Israel',
		'Italy',
		'Jamaica',
		'Japan',
		'Jordan',
		'Kazakhstan',
		'Kenya',
		'Kiribati',
		'Korea, Democratic People\'s Republic of',
		'Korea, Republic of',
		'Kuwait',
		'Kyrgyzstan',
		'Lao, People\'s Democratic Republic',
		'Latvia',
		'Lebanon',
		'Lesotho',
		'Liberia',
		'Libyan Arab Jamahiriya',
		'Liechtenstein',
		'Lithuania',
		'Luxembourg',
		'Macau',
		'Macedonia, The Former Yugoslav Republic of',
		'Madagascar',
		'Malawi',
		'Malaysia',
		'Maldives',
		'Mali',
		'Malta',
		'Marshall Islands',
		'Martinique',
		'Mauritania',
		'Mauritius',
		'Mayotte',
		'Mexico',
		'Micronesia, Federated States of',
		'Moldova, Republic of',
		'Monaco',
		'Mongolia',
		'Montserrat',
		'Morocco',
		'Mozambique',
		'Myanmar',
		'Namibia',
		'Nauru',
		'Nepal',
		'Netherlands',
		'Netherlands Antilles',
		'New Caledonia',
		'New Zealand',
		'Nicaragua',
		'Niger',
		'Nigeria',
		'Niue',
		'Norfolk Island',
		'Northern Mariana Islands',
		'Norway',
		'Oman',
		'Pakistan',
		'Palau',
		'Panama',
		'Papua New Guinea',
		'Paraguay',
		'Peru',
		'Philippines',
		'Pitcairn',
		'Poland',
		'Portugal',
		'Puerto Rico',
		'Qatar',
		'Reunion',
		'Romania',
		'Russian Federation',
		'Rwanda',
		'Saint Kitts and Nevis',
		'Saint Lucia',
		'Saint Vincent and the Grenadines',
		'Samoa',
		'San Marino',
		'Sao Tome and Principe',
		'Saudi Arabia',
		'Senegal',
		'Seychelles',
		'Sierra Leone',
		'Singapore',
		'Slovakia (Slovak Republic)',
		'Slovenia',
		'Solomon Islands',
		'Somalia',
		'South Africa',
		'South Georgia and the South Sandwich Islands',
		'Spain',
		'Sri Lanka',
		'St. Helena',
		'St. Pierre and Miquelon',
		'Sudan',
		'Suriname',
		'Svalbard and Jan Mayen Islands',
		'Swaziland',
		'Sweden',
		'Switzerland',
		'Syrian Arab Republic',
		'Taiwan, Province of China',
		'Tajikistan',
		'Tanzania, United Republic of',
		'Thailand',
		'Togo',
		'Tokelau',
		'Tonga',
		'Trinidad and Tobago',
		'Tunisia',
		'Turkey',
		'Turkmenistan',
		'Turks and Caicos Islands',
		'Tuvalu',
		'Uganda',
		'Ukraine',
		'UAE',
		'UK',
		'USA',
		'United States Minor Outlying Islands',
		'Uruguay',
		'Uzbekistan',
		'Vanuatu',
		'Venezuela',
		'Vietnam',
		'Virgin Islands (British)',
		'Virgin Islands (U.S.)',
		'Wallis and Futuna Islands',
		'Western Sahara',
		'Yemen',
		'Yugoslavia',
		'Zambia',
		'Zimbabwe'
	];

	private $countriesWithNoTours = [];


    /**
     * Make slugs for Locations that don't have ones
     */
    public function makeSlugs () {
        $allLocationsEn = Location::where('lang', 'en')->get();
        foreach ($allLocationsEn as $location) {
            $this->addSlug($location->api_id, $location->name);
        }
    }


    /**
     * Add LocationSlug
     *
     * @param integer $api_id
     * @param string $name - location name
     */
    private function addSlug ( $api_id, $name ) {
        LocationSlug::firstOrCreate([
            'api_id' => $api_id,
            'slug' => SlugController::slugFromName($name)
        ]);
    }


	/**
	 * Save location
	 *
	 * @param array $locationData
	 *
	 * @return null|int
	 */
	public function store($locationData) {
		$saved = Location::create($locationData);
		if ( !$saved ) return null;
		return $saved->id;
	}


	/**
	 * Get tour list via API inside $locationName location
	 *
	 * @param string $locationName
	 * @param string $lang
	 * @param int $limit
	 * @param int $offset
	 *
	 * @return null
	 */
	private function getLocationsByAPI ($locationName, $lang, $limit=10, $offset=0, $wholeResp=false) {
		$locationName = urlencode($locationName);
		$response = GetYourGuideApiController::request(
			"tours?q=$locationName&limit=$limit&offset=$offset",
			$lang
		);
		if ( !$response || empty($response['data']) || empty($response['data']['tours']) ) {
			return null;
		}
		if ( $wholeResp )
			return $response;
		return $response['data']['tours'];
	}


	/**
	 * Save all locations inside $country
	 *
	 * @param string $country
	 * @param string $lang
	 */
	private function parseCountry($country, $lang) {
		$offset = 0;
		$parserStep = 100;

		// send requests to API, page by page
		while ( true ) {

			// get tours by API
			$this->log("Получаем туры по API. Offset: $offset");
			$tours = $this->getLocationsByAPI($country, $lang, $parserStep, $offset);
			if ( !$tours ) {
				if ( $offset === 0 )
					$this->countriesWithNoTours[] = $country;
				$this->log("Туров в стране не осталось, либо страна не существует в базе GetYourGuide", true);
				break;
			}

			// save locations of these tours
			$this->log("Туры получены, начинаем парсинг");
			$this->saveLocations($tours, $lang);

			// change "page number" for API requests
			$offset += $parserStep;
			sleep(1);
		}

	}


	/**
	 * Save locations from batch of tours
	 *
	 * @param array $tours
	 * @param string $lang
	 */
	private function saveLocations ($tours, $lang) {
		foreach ($tours as $tour) {
			if ( empty($tour['locations']) ) {
				$this->log("У тура нет локаций", true);
				continue;
			}
			foreach ($tour['locations'] as $location) {

				$locationName = $location['name'];

				// if location exists - skip
				$exists = Location::where([
					'name' => $locationName,
					'lang' => $lang
				])->first();
				if ( $exists ) {
					$this->log("Локация существует: $locationName");
					continue;
				}

				// if location is not added yet - add
				$savedID = $this->store(array_merge($location, [
					'api_id' => $location['location_id'],
					'api_parent_id' => empty($location['parent_id']) ? null : $location['parent_id'],
					'country_code' => $location['country'],
					'lat' => $location['coordinates']['lat'],
					'long' => $location['coordinates']['long'],
					'lang' => $lang
				]));
				$this->log("Локация сохранена: $locationName, ID: $savedID");
			}
		}
	}


	/**
	 * Start parser. Parse all existing locations.
	 *
	 * @param null|Command $command
	 */
	public function parseAll($command=null) {

		// enable logging to console
		if ( $command )
			$this->command = $command;

		$this->log("Start parsing...");

		$lang = 'de';

		foreach (self::COUNTRY_LIST as $country) {
			$this->log("...");
			$this->log("Start parsing country: $country");
			$this->parseCountry($country, $lang);
			$this->log("End parsing country: $country");
			$this->log("...");
		}

		$this->log("Save empty countries for language: $lang");
		$this->logCountriesWithNoTours($lang);

		$this->log("Finished!");
	}


	/**
	 * Add Total Tours count for every Location
	 *
	 * @param null $command
	 */
	public function addTotals($command=null) {
		// enable logging to console
		if ( $command )
			$this->command = $command;

		$this->log("Start...");

		$locations = Location::where('lang', 'en')->get();
		foreach ($locations as $location) {
			$this->log("Location: $location->name");
			$locationApiData = $this->getLocationsByAPI(
				$location->name,
				"en",
				1,
				0,
				true
			);
			$toursCount = $locationApiData["_metadata"]["totalCount"];
			$this->log("Tours Total: $toursCount");
			$location->total_tours = $toursCount;
			$location->save();
			$this->log('Saved');
			sleep(1);
		}
	}


	/**
	 * Add background image for every Location
	 *
	 * @param null $command
	 */
	public function addBgs($command=null) {
		$bgFormatId = '92';

		// enable logging to console
		if ( $command )
			$this->command = $command;

		$this->log("Start...");

		$locations = Location::where('lang', 'en')->get();
		foreach ($locations as $location) {
			$this->log("Location: $location->name");
			$locationApiData = $this->getLocationsByAPI(
				$location->name,
				"en",
				1,
				0
			);
			if ( !$locationApiData || empty($locationApiData[0]["pictures"]) )
				continue;
			//$bg = str_replace(
			//	'[format_id]',
			//	$bgFormatId,
			//	$locationApiData[0]["pictures"][0]["url"]
			//);
			$bg = $locationApiData[0]["pictures"][0]["url"];
			$this->log('BG: ' . $bg);
			$location->bg = $bg;
			$location->save();
			$this->log('Saved');
			sleep(1);
		}
	}


	/**
	 * Save empty countries to log file
	 *
	 * @param string $lang
	 */
	private function logCountriesWithNoTours ($lang) {
		$logFile = storage_path()."/logs/empty_countries_$lang.txt";
		file_put_contents(
			$logFile,
			implode(PHP_EOL, $this->countriesWithNoTours)
		);
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
