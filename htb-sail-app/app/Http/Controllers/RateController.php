<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RateController extends Controller
{
    public function FormCheck() {
        return response()->json(['name' => 'Abigail', 'state' => 'CA']);
    }
    
    public function ProcessRate(Request $request) {
        $formData = $request->all();

        $rate = $request->rate;
        $cdr = $request->cdr;

        $consumedkWh = $this->calculateConsumedkWh($cdr['meterStart'], $cdr['meterStop']);
        $energyPrice = $this->calculateEnergyPrice($consumedkWh, $rate['energy']);
        
        return response()->json([
            'overall' => $cdr,
            'components:' => ['energy:' => $energyPrice,]
        ]);
    }

    private function calculateConsumedkWh($meterStart, $meterStop) {
        return $meterStop - $meterStart;
    }

    private function calculateEnergyPrice($consumedkWh, $energy) {
        return round($consumedkWh/1000 * $energy, 3);
    }
}
