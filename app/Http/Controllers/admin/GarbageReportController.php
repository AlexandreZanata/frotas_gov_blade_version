<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\garbage\GarbageRun;
use Illuminate\Http\Request;

class GarbageReportController extends Controller
{
    public function index(Request $request)
    {
        $runs = GarbageRun::with(['garbageVehicle.vehicle', 'garbageUser.user', 'destinations.neighborhood'])
            ->latest()
            ->paginate(10);

        return view('admin.garbage-reports.index', compact('runs'));
    }
}
