<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobListing;
use App\Http\Resources\JobResource;
use App\Http\Requests\StoreJobRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = JobListing::with(['company', 'category', 'jobType'])->active();

        $query->filter($request->only(['category', 'search']));

        return JobResource::collection($query->paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJobRequest $request)
    {
        // Add slug and company_id to validated data
        $data = $request->validated();
        $data['slug'] = Str::slug($data['title']) . '-' . Str::random(6);

        // Assuming user has a company. In a real app, we'd check this relation.
        // For this demo, we assume the user->company relation exists or we find the company.
        $company = $request->user()->company;

        if (!$company) {
            return response()->json(['message' => 'User does not have a company profile.'], 403);
        }

        $job = $company->jobs()->create($data);

        return new JobResource($job);
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $job = JobListing::with(['company', 'category', 'jobType'])
            ->where('slug', $slug)
            ->firstOrFail();

        return new JobResource($job);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $job = JobListing::findOrFail($id);

        // Authorization check: Ensure the user owns the company that owns the job
        if ($job->company_id !== request()->user()->company->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $job->delete();

        return response()->json(['message' => 'Job deleted successfully']);
    }
}
