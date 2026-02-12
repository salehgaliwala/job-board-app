<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\JobListing;

class CandidateController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'seeker')->with('skills');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(
                fn($q) =>
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
            );
        }

        if ($request->has('skills')) {
            $skillIds = $request->input('skills'); // array
            if (!empty($skillIds)) {
                $query->whereHas('skills', fn($q) => $q->whereIn('skills.id', $skillIds));
                $query->withCount(['skills' => fn($q) => $q->whereIn('skills.id', $skillIds)]);
                $query->orderByDesc('skills_count');
            }
        }

        return response()->json($query->paginate(15));
    }

    public function match(Request $request, $jobId)
    {
        $job = JobListing::with('skills')->findOrFail($jobId);
        $jobSkillIds = $job->skills->pluck('id')->toArray();

        if (empty($jobSkillIds)) {
            return response()->json([]);
        }

        $candidates = User::where('role', 'seeker')
            ->whereHas('skills', fn($q) => $q->whereIn('skills.id', $jobSkillIds))
            ->with('skills')
            ->withCount(['skills' => fn($q) => $q->whereIn('skills.id', $jobSkillIds)])
            ->orderByDesc('skills_count')
            ->paginate(15);

        return response()->json($candidates);
    }
}
