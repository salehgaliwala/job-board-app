<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'location' => $this->location,
            'salary_range' => $this->salary_range,
            'status' => $this->status,
            'created_at' => $this->created_at->diffForHumans(),
            'company' => [
                'name' => $this->company->name,
                'logo' => $this->company->logo,
                'slug' => $this->company->slug,
            ],
            'category' => $this->category->name,
            'type' => $this->jobType->name,
        ];
    }
}
