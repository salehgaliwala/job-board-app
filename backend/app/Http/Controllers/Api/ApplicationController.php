<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\JobListing;
use App\Mail\JobAppliedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ApplicationController extends Controller
{
    public function employerIndex(Request $request)
    {
        $user = $request->user();

        // Ensure user has a company profile
        if (!$user->company) {
            return response()->json([
                'message' => 'User does not have a company profile.'
            ], 403);
        }

        // Fetch jobs posted by this company with their applications
        $jobs = $user->company->jobs()
            ->with(['applications.user']) // Load applications and the applicant details
            ->get();

        return response()->json($jobs);
    }

    public function seekerIndex(Request $request)
    {
        $user = $request->user();
        $applications = Application::where('user_id', $user->id)
            ->with(['job.company'])
            ->latest()
            ->get();

        return response()->json($applications);
    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'resume' => 'required|file|mimes:pdf,doc,docx|max:2048',
            'cover_letter' => 'nullable|string',
        ]);

        $job = JobListing::findOrFail($id);
        $user = $request->user();

        // Check if already applied
        $existingApplication = Application::where('job_id', $job->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingApplication) {
            return response()->json(['message' => 'You have already applied for this job.'], 400);
        }

        // Store resume
        $path = $request->file('resume')->store('resumes', 'public');

        $application = Application::create([
            'job_id' => $job->id,
            'user_id' => $user->id,
            'resume_path' => $path,
            'cover_letter' => $request->cover_letter,
            'status' => 'pending',
        ]);

        // Send email to employer
        $employer = $job->company->user;
        if ($employer && $employer->email) {
            try {
                Mail::to($employer->email)->send(new JobAppliedMail($application));
            } catch (\Exception $e) {
                Log::error('Failed to send job application email: ' . $e->getMessage());
            }
        }

        return response()->json([
            'message' => 'Application submitted successfully!',
            'application' => $application
        ], 201);
    }
}
