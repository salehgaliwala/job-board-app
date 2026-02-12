<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function updateSkills(Request $request)
    {
        $request->validate(['skills' => 'array', 'skills.*' => 'exists:skills,id']);
        $request->user()->skills()->sync($request->input('skills', []));
        return response()->json(['message' => 'Skills updated successfully.']);
    }
}
