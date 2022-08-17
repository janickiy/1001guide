<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GetYourGuideApiController extends Controller
{

	private const API_BASE_URL = "https://api.getyourguide.com/1/";
	private const API_ACCESS_TOKEN = 'dsepUDtYXmUEc824oXfMKXjPdrwqRLgogOZEQU6s3kjUziuU';


	public static function request ($requestUrl, $lang='en', $currency='eur', $limit=null, $offset=null) {
		$requestUrl = self::API_BASE_URL.$requestUrl."&cnt_language=$lang&currency=$currency";
		if ( $limit )
			$requestUrl .= "&limit=$limit";
		if ( $offset )
			$requestUrl .= "&offset=$offset";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $requestUrl);
		curl_setopt($ch, CURLOPT_POST, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$headers = [
			'X-ACCESS-TOKEN: ' . self::API_ACCESS_TOKEN,
			'Accept: application/json'
		];

		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$server_output = curl_exec ($ch);
		curl_close ($ch);

		if ( !$server_output ) return null;

		$jsonResponse = json_decode($server_output, true);

		//file_put_contents(
		//	__DIR__.'/logs.txt', "request: $requestUrl" . PHP_EOL,
		//	FILE_APPEND
		//);
		//file_put_contents(
		//	__DIR__.'/logs.txt', "response: " . var_export($jsonResponse, true) . PHP_EOL . PHP_EOL,
		//	FILE_APPEND
		//);

		return $jsonResponse;
	}

}
