<?php

namespace App\Http\Controllers;

use App\Currency;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{


	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index () {
		$currencies = Currency::all();

		return response()->json([
			'items' => $currencies
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

		$currency = Currency::find($id);

		return response()->json([
			'item' => $currency ? $currency : array()
		]);
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {

		// save current translation
		$saved = Currency::create($request->all());

		// response
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
		Currency::find($id)->update($request->all());
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
		Currency::find($id)->delete();
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
			Currency::find((int)$id)->delete();
		}
		return response()->json([
			'deleted_all' => true,
			'success' => 'Элемент удалён',
			'ids' => $request->input('ids')
		]);
	}


}
