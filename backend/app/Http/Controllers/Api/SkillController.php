<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Skill;

class SkillController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('query');
        $skills = Skill::query()
            ->when($query, fn($q) => $q->where('name', 'like', "%{$query}%"))
            ->limit(20)
            ->get();
        return response()->json($skills);
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|unique:skills,name']);
        $skill = Skill::create(['name' => $request->name]);
        return response()->json($skill, 201);
    }
}
