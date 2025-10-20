<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\garbage\GarbageNeighborhood;
use Illuminate\Http\Request;

class GarbageNeighborhoodController extends Controller
{
    public function index()
    {
        $neighborhoods = GarbageNeighborhood::paginate(10);
        return view('admin.garbage-neighborhoods.index', compact('neighborhoods'));
    }

    public function create()
    {
        return view('admin.garbage-neighborhoods.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:garbage_neighborhoods,name'
        ]);

        GarbageNeighborhood::create($request->only('name'));

        return redirect()->route('admin.garbage-neighborhoods.index')->with('success', 'Bairro criado com sucesso.');
    }

    public function edit(GarbageNeighborhood $garbageNeighborhood)
    {
        return view('admin.garbage-neighborhoods.edit', compact('garbageNeighborhood'));
    }

    public function update(Request $request, GarbageNeighborhood $garbageNeighborhood)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:garbage_neighborhoods,name,' . $garbageNeighborhood->id
        ]);

        $garbageNeighborhood->update($request->only('name'));

        return redirect()->route('admin.garbage-neighborhoods.index')->with('success', 'Bairro atualizado com sucesso.');
    }

    public function destroy(GarbageNeighborhood $garbageNeighborhood)
    {
        $garbageNeighborhood->delete();

        return redirect()->route('admin.garbage-neighborhoods.index')->with('success', 'Bairro excluído com sucesso.');
    }
}
