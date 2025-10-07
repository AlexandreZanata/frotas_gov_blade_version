<?php

namespace App\Http\Controllers;

use App\Models\VehicleCategory;
use Illuminate\Http\Request;

class VehicleCategoryController extends Controller
{
    public function index()
    {
        $categories = VehicleCategory::latest()->paginate(10);
        return view('vehicle_categories.index', compact('categories'));
    }

    public function create()
    {
        return view('vehicle_categories.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255|unique:vehicle_categories,name']);
        VehicleCategory::create($request->only('name'));
        return redirect()->route('vehicle-categories.index')->with('success', 'Categoria criada com sucesso.');
    }

    public function edit(VehicleCategory $vehicleCategory)
    {
        return view('vehicle_categories.edit', compact('vehicleCategory'));
    }

    public function update(Request $request, VehicleCategory $vehicleCategory)
    {
        $request->validate(['name' => 'required|string|max:255|unique:vehicle_categories,name,' . $vehicleCategory->id]);
        $vehicleCategory->update($request->only('name'));
        return redirect()->route('vehicle-categories.index')->with('success', 'Categoria atualizada com sucesso.');
    }

    public function destroy(VehicleCategory $vehicleCategory)
    {
        $vehicleCategory->delete();
        return redirect()->route('vehicle-categories.index')->with('success', 'Categoria excluída com sucesso.');
    }
}
