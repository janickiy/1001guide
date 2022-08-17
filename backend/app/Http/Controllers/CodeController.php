<?php

namespace App\Http\Controllers;

use App\Code;
use Illuminate\Http\Request;

class CodeController extends Controller
{
	public function frontend () {
		$codes = Code::all();
		return response()->json([
			'items' => $codes
		]);
	}


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $codes = Code::all();
	    return response()->json([
		    'items' => $codes
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
	    $saved = Code::create($request->all());
	    return response()->json([
		    'stored' => true,
		    'id' => $saved->id
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
		    'item' => Code::find($id)
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
	    Code::find($id)->update($request->all());
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
	    Code::find($id)->delete();
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
			Code::find((int)$id)->delete();
		}
		return response()->json([
			'deleted_all' => true,
			'success' => 'Элемент удалён',
			'ids' => $request->input('ids')
		]);
	}
}
