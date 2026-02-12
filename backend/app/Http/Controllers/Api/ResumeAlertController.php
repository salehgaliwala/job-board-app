<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ResumeAlert;

class ResumeAlertController extends Controller
{
    public function index(Request $request)
    {
        return response()->json($request->user()->resumeAlerts);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'keywords' => 'nullable|string',
            'search_criteria' => 'nullable|array'
        ]);

        $alert = $request->user()->resumeAlerts()->create($validated);
        return response()->json($alert, 201);
    }

    public function destroy(ResumeAlert $resumeAlert)
    {
        if ($resumeAlert->user_id !== auth()->id()) {
            abort(403);
        }
        $resumeAlert->delete();
        return response()->noContent();
    }
}
