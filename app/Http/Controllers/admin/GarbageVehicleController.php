<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\garbage\GarbageVehicle;
use App\Models\Vehicle\Vehicle;
use Illuminate\Http\Request;

class GarbageVehicleController extends Controller
{
    public function index()
    {
        $vehicles = GarbageVehicle::with('vehicle.prefix', 'vehicle.category')->paginate(10);
        return view('admin.garbage-vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        $availableVehicles = Vehicle::whereNotIn('id', function($query) {
            $query->select('vehicle_id')->from('garbage_vehicles');
        })->get();

        return view('admin.garbage-vehicles.create', compact('availableVehicles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id|unique:garbage_vehicles,vehicle_id'
        ]);

        GarbageVehicle::create($request->only('vehicle_id'));

        return redirect()->route('admin.garbage-vehicles.index')->with('success', 'Veículo de lixo adicionado com sucesso.');
    }

    public function destroy(GarbageVehicle $garbageVehicle)
    {
        $garbageVehicle->delete();

        return redirect()->route('admin.garbage-vehicles.index')->with('success', 'Veículo de lixo removido com sucesso.');
    }
}
