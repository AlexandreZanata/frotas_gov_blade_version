<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\garbage\GarbageUser;
use App\Models\garbage\GarbageUserVehicle;
use App\Models\garbage\GarbageVehicle;
use App\Models\garbage\GarbageNeighborhood;
use App\Models\garbage\GarbageUserNeighborhood;
use App\Models\user\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GarbageUserManagementController extends Controller
{
    public function index()
    {
        $garbageUsers = GarbageUser::with(['user', 'vehicles.vehicle', 'neighborhoods'])->paginate(10);
        return view('admin.garbage-users.index', compact('garbageUsers'));
    }

    public function create()
    {
        $users = User::whereNotIn('id', function($query) {
            $query->select('user_id')->from('garbage_users');
        })->get();

        return view('admin.garbage-users.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id|unique:garbage_users,user_id'
        ]);

        GarbageUser::create($request->only('user_id'));

        return redirect()->route('admin.garbage-users.index')->with('success', 'Usuário de lixo criado com sucesso.');
    }

    public function show(GarbageUser $garbageUser)
    {
        $garbageUser->load(['user', 'vehicles.vehicle', 'neighborhoods']);
        return view('admin.garbage-users.show', compact('garbageUser'));
    }

    public function edit(GarbageUser $garbageUser)
    {
        return view('admin.garbage-users.edit', compact('garbageUser'));
    }

    public function update(Request $request, GarbageUser $garbageUser)
    {
        // Atualização básica, se necessário adicionar mais campos
        return redirect()->route('admin.garbage-users.index')->with('success', 'Usuário atualizado com sucesso.');
    }

    public function destroy(GarbageUser $garbageUser)
    {
        $garbageUser->delete();

        return redirect()->route('admin.garbage-users.index')->with('success', 'Usuário de lixo excluído com sucesso.');
    }

    public function editVehicles(GarbageUser $garbageUser)
    {
        $vehicles = GarbageVehicle::with('vehicle')->get();
        $userVehicles = $garbageUser->vehicles->pluck('id')->toArray();

        return view('admin.garbage-users.vehicles', compact('garbageUser', 'vehicles', 'userVehicles'));
    }

    public function updateVehicles(Request $request, GarbageUser $garbageUser)
    {
        $request->validate([
            'vehicles' => 'array',
            'vehicles.*' => 'exists:garbage_vehicles,id'
        ]);

        $garbageUser->vehicles()->sync($request->vehicles ?? []);

        return redirect()->route('admin.garbage-users.index')->with('success', 'Veículos do usuário atualizados com sucesso.');
    }

    public function editNeighborhoods(GarbageUser $garbageUser)
    {
        $neighborhoods = GarbageNeighborhood::all();
        $userNeighborhoods = $garbageUser->neighborhoods->pluck('id')->toArray();

        return view('admin.garbage-users.neighborhoods', compact('garbageUser', 'neighborhoods', 'userNeighborhoods'));
    }

    public function updateNeighborhoods(Request $request, GarbageUser $garbageUser)
    {
        $request->validate([
            'neighborhoods' => 'array',
            'neighborhoods.*' => 'exists:garbage_neighborhoods,id'
        ]);

        $garbageUser->neighborhoods()->sync($request->neighborhoods ?? []);

        return redirect()->route('admin.garbage-users.index')->with('success', 'Bairros do usuário atualizados com sucesso.');
    }
}
