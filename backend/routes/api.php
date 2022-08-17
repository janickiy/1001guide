<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


/*
 * Locale
 */
Route::post( 'locale/{lang}', 'CoreController@frontend' );


/*
 * Languages
 */

// Frontend
Route::post( 'languages/get', 'LanguageController@verifyOrSetDefault' );
Route::post( 'languages/list', 'LanguageController@codesArrayJson' );

// Backend
Route::delete( 'languages/multiple', 'LanguageController@destroyMultiple' );
Route::get( 'languages/codes', 'LanguageController@codes' );
Route::resource( 'languages', 'LanguageController' );
Route::resource( 'settings', 'LocalSettingsController' );



/*
 * Currencies
 */

// Frontend
Route::post( 'currencies/list', 'LanguageController@verifyOrSetDefault' );

// Backend
Route::delete( 'currencies/multiple', 'CurrencyController@destroyMultiple' );
Route::resource( 'currencies', 'CurrencyController' );



/*
 * Locations
 */

// Search
Route::post( 'locations/search', 'LocationController@search' );

// Bg
Route::post( 'locations/load-bg/{slug}', 'LocationController@addBg' );
Route::post( 'locations/update-bg', 'LocationController@updateBgForAll' );

// Show Location
Route::post( 'locations/country/find/{lang}/{slug}', 'CountryController@frontend' );
Route::post( 'locations/find/{lang}/{slug}', 'LocationController@frontend' );
Route::post( 'locations/list/{lang}/{country}', 'LocationController@frontendList' );
Route::post( 'locations/some/{lang}', 'LocationController@someLocations' );

// Countries Backend
Route::resource( 'countries', 'CountryController' );
Route::get( 'locations/country/{parent_id}', 'LocationController@index' );
Route::post( 'locations', 'LocationController@store' );
Route::get( 'locations/all', 'LocationController@index' );

// Cities Backend
Route::get( 'locations/{id}', 'LocationController@show' );
Route::put( 'locations/{id}', 'LocationController@update' );
Route::delete( 'locations/multiple', 'LocationController@destroyMultiple' );
Route::delete( 'locations/country/{parent_id}/{id}', 'LocationController@destroyLocation' );

// POI
Route::post( 'poi', 'LocationController@poi' );


/*
 * Tags
 */

// Backend
Route::delete( 'tags/multiple', 'TagController@destroyMultiple' );
Route::resource( 'tags', 'TagController' );

// Frontend
Route::post( 'tags/list/{lang}', 'TagController@list' );
Route::post( 'tags/{lang}/{slug}', 'TagController@frontend' );


/*
 * Tours
 */
Route::post('tours/{lang}/{location}', 'ToursController@all');


/*
 * Templates
 */
Route::get('templates/{lang}/{pageType}/{field}/{tagId}', 'TemplateFieldValueController@index');
Route::get('templates/{lang}/{pageType}/{field}', 'TemplateFieldValueController@index');
Route::put('templates/{lang}/{pageType}/{field}', 'TemplateFieldValueController@save');

Route::post('templates/generate/status', 'GenerationStatusController@checkCommon');
Route::post('templates/generate', 'TemplateFieldValueController@generate');


/*
 * Codes
 */

// Frontend
Route::post( 'codes/all', 'CodeController@frontend' );

// Backend
Route::delete( 'codes/multiple', 'CodeController@destroyMultiple' );
Route::resource( 'codes', 'CodeController' );


/*
 * Sitemap
 */
Route::post( 'sitemap/web/{lang}', 'SitemapController@web' );


/*
 * Export / Import
 */
Route::get( 'export/{lang}/{type}', 'ExportController@export' );
Route::post( 'import/{lang}/{type}', 'ImportController@import' );


/*
 * Parsers and generators
 */
Route::get('make_slugs', 'LocationParserController@makeSlugs');
Route::get('parse_countries', 'CountryParserController@parseAll');
Route::get('make_country_slugs', 'CountryParserController@makeSlugs');
