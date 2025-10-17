<?php

namespace App\Http\Controllers;

use App\Models\fuel\GasStationCurrent;

class GasStationCurrentController extends Controller
{
    public function index()
    {
        $currentGasStations = GasStationCurrent::with('gasStation')
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where(function ($query) {
                $query->where('end_date', '>=', now())
                    ->orWhereNull('end_date');
            })
            ->paginate(10);

        return view('gas_stations_current.index', compact('currentGasStations'));
    }
}
