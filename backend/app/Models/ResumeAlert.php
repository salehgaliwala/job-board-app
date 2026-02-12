<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResumeAlert extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'search_criteria' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
