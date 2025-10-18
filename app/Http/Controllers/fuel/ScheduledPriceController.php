<?php

namespace App\Http\Controllers\fuel;

use App\Http\Controllers\Controller;
use App\Models\fuel\FuelType;
use App\Models\fuel\GasStation;
use App\Models\fuel\ScheduledPrice;
use Illuminate\Http\Request;

class ScheduledPriceController extends Controller
{
    public function index()
    {
        $scheduledPrices = ScheduledPrice::with('gasStation', 'fuelType', 'admin')->latest()->paginate(10);
        return view('scheduled_prices.index', compact('scheduledPrices'));
    }

    public function create()
    {
        $gasStations = GasStation::where('status', 'active')->orderBy('name')->get();
        $fuelTypes = FuelType::orderBy('name')->get();
        return view('scheduled_prices.create', compact('gasStations', 'fuelTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'gas_station_id' => 'required|exists:gas_stations,id',
            'fuel_type_id' => 'required|exists:fuel_types,id',
            'price' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        ScheduledPrice::create($request->all() + ['admin_id' => auth()->id()]);

        return redirect()->route('scheduled_prices.index')->with('success', 'Agendamento de preço criado com sucesso.');
    }

    public function edit(ScheduledPrice $scheduledPrice)
    {
        $gasStations = GasStation::where('status', 'active')->orderBy('name')->get();
        $fuelTypes = FuelType::orderBy('name')->get();
        return view('scheduled_prices.edit', compact('scheduledPrice', 'gasStations', 'fuelTypes'));
    }

    public function update(Request $request, ScheduledPrice $scheduledPrice)
    {
        $request->validate([
            'gas_station_id' => 'required|exists:gas_stations,id',
            'fuel_type_id' => 'required|exists:fuel_types,id',
            'price' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $scheduledPrice->update($request->all());

        return redirect()->route('scheduled_prices.index')->with('success', 'Agendamento de preço atualizado com sucesso.');
    }

    public function destroy(ScheduledPrice $scheduledPrice)
    {
        $scheduledPrice->delete();
        return redirect()->route('scheduled_prices.index')->with('success', 'Agendamento de preço excluído com sucesso.');
    }
}
