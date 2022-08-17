<?php

namespace App\Http\Controllers;

use App\GenerationStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GenerationStatusController extends Controller
{


	public static function setStatus ($type, $status) {
		GenerationStatus::updateOrCreate(
			['type' => $type],
			[
				'status' => $status,
			]
		);
	}


	public static function getStatus ($type) {
		$statusCollection = GenerationStatus::where('type', $type)->first();
		if ( !$statusCollection || !$statusCollection->count() )
			return null;
		return $statusCollection->status;
	}


	public static function startCommon () {
		self::setStatus('all', 'in_progress');
	}

	public static function finishCommon () {
		self::setStatus('all', 'finished');
	}

	public static function cancelCommon () {
		self::setStatus('all', 'cancelled');
	}


	public function checkCommon () {
		return response()->json([
			'status' => self::getStatus('all')
		]);
	}


	/**
	 * Set generation started
	 *
	 * @param string $type
	 * @param int $total
	 */
	public static function start ($type, $total) {
		GenerationStatus::updateOrCreate(
			['type' => $type],
			[
				'total' => $total,
				'current' => 0,
				'is_finished' => false
			]
		);
	}


	public static function finish ($type) {
		$status = GenerationStatus::where('type', $type)->first();
		$status->is_finished = true;
		$status->save();
	}


	/**
	 * Get difference from Date to now in minutes
	 *
	 * @param $datetime
	 *
	 * @return int
	 */
	private static function minutesPassed ($datetime) {
		$now = Carbon::now();
		return $now->diffInMinutes($datetime);
	}

}
