<?php

namespace App\Http\Controllers;

use App\Models\fuel\FuelPrice;

class FuelPriceController extends Controller
{
    public function index()
    {
        $fuelPrices = FuelPrice::with('gasStation', 'fuelType')->latest('effective_date')->paginate(15);
        return view('fuel_prices.index', compact('fuelPrices'));
    }
}
