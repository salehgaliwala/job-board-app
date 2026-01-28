<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class JobListing extends Model
{
    /** @use HasFactory<\Database\Factories\JobListingFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function jobType(): BelongsTo
    {
        return $this->belongsTo(JobType::class);
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('status', 'active');
    }

    public function scopeFilter(Builder $query, array $filters): void
    {
        if ($filters['category'] ?? false) {
            $query->whereHas(
                'category',
                fn($q) =>
                $q->where('slug', $filters['category'])
            );
        }

        if ($filters['search'] ?? false) {
            $query->where(
                fn($q) =>
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('description', 'like', '%' . $filters['search'] . '%')
            );
        }
    }
}
