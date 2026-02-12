<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    protected $fillable = ['name'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'skill_user');
    }

    public function jobs()
    {
        return $this->belongsToMany(JobListing::class, 'job_skill', 'skill_id', 'job_listing_id');
    }
}
