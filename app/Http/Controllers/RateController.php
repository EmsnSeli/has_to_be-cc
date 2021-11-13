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

        //calculate used time
        $timestamp = $this->calculateTimestamp($cdr['timestampStart'], $cdr['timestampStop']);
        //calculate time price with time used
        $timePrice = $this->calculateTimePrice($timestamp, $rate['time']);

        //calculate overall price with all variables
        $overall = $this->calculateOverall($energyPrice, $timePrice, $rate['transaction']);

        //build return json with calculated values
        return response()->json([
            'overall' => $overall,
            'components:' => [
                'energy:' => $energyPrice,
                'time:' => $timePrice,
                'transaction:' => $rate['transaction']]
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

    //time calculated in hours
    private function calculateTimestamp($timestampStart, $timestampStop) {
        return (strtotime($timestampStop) - strtotime($timestampStart)) / 60 / 60;
    }

    //time price calculated and rounded up to 3 decimals
    private function calculateTimePrice($timestamp, $time) {
        return round($timestamp * $time, 3);
    }

    //calculate overall price
    private function calculateOverall($energyPrice, $timePrice, $transactionFee) {
        return round($energyPrice + $timePrice + $transactionFee, 2);
    }
}
