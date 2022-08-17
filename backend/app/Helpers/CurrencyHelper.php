<?php

namespace App\Helpers;

use danielme85\CConverter\Currency;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CurrencyHelper
{
    public static function convert($value, $currencyFrom, $currencyTo){

        $currencyFrom = Str::upper($currencyFrom);
        $currencyTo = Str::upper($currencyTo);

        if($currencyTo != $currencyFrom){

            $cacheKey = $currencyFrom;

            $rates = Cache::get($cacheKey);

            if(is_null($rates) || !isset($rates[$currencyTo]) || $rates[$currencyTo] < 0.000000000001){

                try{

                    $rates = Currency::rates($currencyFrom); //, Carbon::now()->subDays(7)->format("Y-m-d")

                    Cache::put($cacheKey, $rates, 86400);

                }catch (\Exception $e){

                    Log::debug($e->getMessage());
                    Log::debug($e->getTraceAsString());
                }
            }

            if(is_array($rates) && isset($rates[$currencyTo])){
                $value = round($rates[$currencyTo] * $value, 0);
            }else{
                $value = 0;
            }
        }

        return $value;
    }
}
