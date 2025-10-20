<?php

namespace App\Http\Controllers\runs;

use App\Http\Controllers\Controller;
use App\Models\run\RunGapFind;
use Illuminate\Http\Request;

class RunGapFindController extends Controller
{
    public function getRecentGaps(Request $request)
    {
        $gaps = RunGapFind::with(['vehicle', 'user', 'run'])
            ->where('created_at', '>=', now()->subDays(7)) // Últimos 7 dias
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json($gaps);
    }
}
