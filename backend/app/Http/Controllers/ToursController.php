<?php

namespace App\Http\Controllers;

use App\Helpers\CurrencyHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ToursController extends Controller
{


	/**
	 * Display tour list
	 *
	 * @param string $lang
	 * @param string $location
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function all($lang, $location, Request $request) {

		// make URL string from Filters in Request
		$filters = $this->inlineFilters($request);

		// make URL
		$requestUrl = "tours?q=$location" . (
			$filters ? $filters : ""
			);

        $originalCurrency = Str::lower($request->input('currency', 'usd'));

        $requestCurrency = ($originalCurrency != "rub") ? $originalCurrency : "eur";

		// Get tours
		$tours = GetYourGuideApiController::request(
			$requestUrl,
			$lang,
            $requestCurrency,
			$request->input('limit', 8),
			$request->input('offset', 0)
		);

        //Log::debug(print_r($tours, true));

        // convert eur to rub
        if($originalCurrency == "rub"){

            if(
                isset($tours['data']['tours']) &&
                is_array($tours['data']['tours'])
            ){
                foreach ($tours['data']['tours'] as $key => $tour) {

                    if(
                        isset($tour['price']) &&
                        isset($tour['price']['values']) &&
                        isset($tour['price']['values']['amount'])
                    ){
                        $tour['price']['values']['amount'] = CurrencyHelper::convert($tour['price']['values']['amount'], $requestCurrency, $originalCurrency);
                    }

                    $tours['data']['tours'][$key] = $tour;
                }
            }

        }

		// return no tours
		if ( !$tours || empty($tours['data']) || empty($tours['data']['tours']) ){

            return response()->json([
                'items' => null,
            ]);
        }

		// return tours
		return response()->json([
			'items' => $tours['data']['tours'],
		]);
	}


	/**
	 * Convert Filters from Request to URL-string
	 *
	 * @param Request $request
	 *
	 * @return string
	 */
	private function inlineFilters ($request) {
		$url = '';

		// categories, flags, languages, price filters
		$url .= $this->inlineArrayFilters($request);

		// duration filter
		$url .= $this->durationFilter($request->input('filterDuration'));

		// date filter
		$url .= $this->dateFilter($request->input('dateFilter'));

		// sorting
		$url .= $this->inlineStringFilters($request);

		return $url;
	}


	/**
	 * Get acceptable string parameters and convert them to URL parameter
	 *
	 * @param Request $request
	 *
	 * @return string
	 */
	private function inlineStringFilters ($request) {
		$url = '';

		// List of String filters. [ClientRequestParam => ApiParamName]
		$stringFilters = [
			'sortBy' => 'sortfield',
			'sortDirection' => 'sortdirection',
		];

		foreach ($stringFilters as $requestParamName => $filterName) {
			$input = $request->input($requestParamName);
			if ( !$input )
				continue;
			$url .= "&$filterName=$input";
		}

		return $url;
	}


	/**
	 * Get acceptable array parameters and convert them to URL string parameter
	 *
	 * @param Request $request
	 *
	 * @return string
	 */
	private function inlineArrayFilters ($request) {
		$url = '';

		// List of Array filters. [ClientRequestParam => ApiParamName]
		$arrayFilters = [
			'tagsChecked' => 'categories',
			'filterFlags' => 'flags',
			'filterLangs' => 'cond_language',
			'filterPrice' => 'price'
		];

		foreach ($arrayFilters as $requestParamName => $filterName) {
			$input = $request->input($requestParamName);
			if ( !$input )
				continue;
			$url .= $this->arrayToUrlParam($filterName, $input);
		}

		return $url;
	}


	/**
	 * Convert Array to String URL-param.
	 * E.g. [value1, value2] to &param[]=value1&param[]=value2
	 *
	 * @param string $paramName
	 * @param array $paramValues
	 *
	 * @return string
	 */
	private function arrayToUrlParam ($paramName, $paramValues) {
		$url = '';
		foreach ($paramValues as $value) {
			$url .= "&".$paramName."[]=$value";
		}
		return $url;
	}


	/**
	 * Convert Duration array to URL string param
	 *
	 * @param $durationArray
	 *
	 * @return string
	 */
	private function durationFilter ($durationArray) {
		if ( !$durationArray )
			return "";

		$paramName = "duration";

		// convert hours to minutes
		$durationInMinutesArray = [];
		foreach ($durationArray as $hours) {
			$durationInMinutesArray[] = $hours*60;
		}

		return $this->arrayToUrlParam($paramName, $durationInMinutesArray);
	}


	/**
	 * Convert Dates array to URL string param
	 *
	 * @param $datesArray
	 *
	 * @return string
	 */
	private function dateFilter ($datesArray) {
		if ( !$datesArray || !$datesArray[0] || !$datesArray[1] )
			return "";

		$paramName = "date";

		// format dates correctly
		$formattedDates = [];
		foreach ($datesArray as $date) {
			$formattedDates[] = substr($date, 0, 10) . "T00:00:00";
		}

		return $this->arrayToUrlParam($paramName, $formattedDates);
	}


}
