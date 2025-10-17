<?php

namespace App\Http\Controllers;

use App\Models\fuel\GasStation;
use App\Models\fuel\ScheduledGasStation;
use Illuminate\Http\Request;

class ScheduledGasStationController extends Controller
{
    public function index()
    {
        $scheduledGasStations = ScheduledGasStation::with('gasStation', 'admin')->latest()->paginate(10);
        return view('scheduled_gas_stations.index', compact('scheduledGasStations'));
    }

    public function create()
    {
        $gasStations = GasStation::where('status', 'active')->orderBy('name')->get();
        return view('scheduled_gas_stations.create', compact('gasStations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'gas_station_id' => 'required|exists:gas_stations,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        ScheduledGasStation::create($request->all() + ['admin_id' => auth()->id()]);

        return redirect()->route('scheduled_gas_stations.index')->with('success', 'Agendamento criado com sucesso.');
    }

    public function edit(ScheduledGasStation $scheduledGasStation)
    {
        $gasStations = GasStation::where('status', 'active')->orderBy('name')->get();
        return view('scheduled_gas_stations.edit', compact('scheduledGasStation', 'gasStations'));
    }

    public function update(Request $request, ScheduledGasStation $scheduledGasStation)
    {
        $request->validate([
            'gas_station_id' => 'required|exists:gas_stations,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $scheduledGasStation->update($request->all());

        return redirect()->route('scheduled_gas_stations.index')->with('success', 'Agendamento atualizado com sucesso.');
    }

    public function destroy(ScheduledGasStation $scheduledGasStation)
    {
        $scheduledGasStation->delete();
        return redirect()->route('scheduled_gas_stations.index')->with('success', 'Agendamento excluído com sucesso.');
    }
}
