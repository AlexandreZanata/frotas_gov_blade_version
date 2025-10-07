<?php

namespace App\Http\Controllers;

use App\Models\Prefix;
use Illuminate\Http\Request;

class PrefixController extends Controller
{
    public function index()
    {
        $prefixes = Prefix::latest()->paginate(10);
        return view('prefixes.index', compact('prefixes'));
    }

    public function create()
    {
        return view('prefixes.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255|unique:prefixes,name']);
        Prefix::create($request->only('name'));
        return redirect()->route('prefixes.index')->with('success', 'Prefixo criado com sucesso.');
    }

    public function edit(Prefix $prefix)
    {
        return view('prefixes.edit', compact('prefix'));
    }

    public function update(Request $request, Prefix $prefix)
    {
        $request->validate(['name' => 'required|string|max:255|unique:prefixes,name,' . $prefix->id]);
        $prefix->update($request->only('name'));
        return redirect()->route('prefixes.index')->with('success', 'Prefixo atualizado com sucesso.');
    }

    public function destroy(Prefix $prefix)
    {
        $prefix->delete();
        return redirect()->route('prefixes.index')->with('success', 'Prefixo excluído com sucesso.');
    }
}
