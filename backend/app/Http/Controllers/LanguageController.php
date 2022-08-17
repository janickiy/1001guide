<?php

namespace App\Http\Controllers;

use App\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class LanguageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $languages = Language::all();
        return response()->json([
            'items' => $languages
        ]);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function codes()
    {
        $languages = Language::all(['code'])->pluck('code');
        return response()->json([
            'items' => $languages
        ]);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json([
            'item' => Language::find($id)
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
        $saved = Language::create($request->all());
        return response()->json([
            'stored' => true,
            'id' => $saved->id
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        Language::find($id)->update($request->all());
        return response()->json([
            'updated' => true,
            'success' => 'Элемент обновлён'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Language::find($id)->delete();
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
            Language::find((int)$id)->delete();
        }
        return response()->json([
            'deleted_all' => true,
            'success' => 'Элемент удалён',
            'ids' => $request->input('ids')
        ]);
    }


	/**
	 * Verify if language code exists and return it.
	 * If it does not - return default language code
	 *
	 * @param Request $request
	 *
	 * @return mixed|string
	 */
    public function verifyOrSetDefault (Request $request) {
	    $langCode = $request->input('code');
	    if ( !$langCode ) {
		    return Language::DEFAULT_LANG;
	    }
    	$languages = self::codesArray();
    	if ( in_array($langCode, $languages) )
    		return $langCode;
    	return Language::DEFAULT_LANG;
    }


    /**
     * Get array of language codes
     *
     * @return array
     */
    public static function codesArray()
    {
        return Language::all(['code'])->pluck('code')->toArray();
    }


    public function codesArrayJson () {
	    return response()->json([
		    'success' => 'true',
		    'items' => self::codesArray()
	    ]);
    }


    private function log ($codes) {
    	$file = storage_path() . "/logs/languages_request.txt";
    	$content = date("Y-m-d H:i:s") .
	               " - IP: " . $_SERVER['REMOTE_ADDR'] .
	               PHP_EOL;
		file_put_contents($file, $content, FILE_APPEND);
    }
}
