<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
}
