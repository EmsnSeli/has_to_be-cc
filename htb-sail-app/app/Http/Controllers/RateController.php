<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RateController extends Controller
{
    //api call here
    public function ProcessRate(Request $request) {

        //get rate object of request
        $rate = $request->rate;
        //get cdr object of request
        $cdr = $request->cdr;

        //calculate consumed kWh
        $consumedkWh = $this->calculateConsumedkWh($cdr['meterStart'], $cdr['meterStop']);
        //with calculated kWh calculate energy price
        $energyPrice = $this->calculateEnergyPrice($consumedkWh, $rate['energy']);
        
        //build return json wit calculated values
        return response()->json([
            'overall' => $cdr,
            'components:' => ['energy:' => $energyPrice,]
        ]);
    }

    //consumed kWh calculated
    private function calculateConsumedkWh($meterStart, $meterStop) {
        return $meterStop - $meterStart;
    }

    //energy calculated and rounded up to 3 decimals
    private function calculateEnergyPrice($consumedkWh, $energy) {
        return round($consumedkWh/1000 * $energy, 3);
    }
}
