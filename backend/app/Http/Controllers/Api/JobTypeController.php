<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\JobType;

class JobTypeController extends Controller
{
    public function index()
    {
        return JobType::all();
    }
}
