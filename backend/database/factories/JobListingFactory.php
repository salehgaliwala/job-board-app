<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Company;
use App\Models\Category;
use App\Models\JobType;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobListing>
 */
class JobListingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->jobTitle();
        return [
            'company_id' => Company::factory(),
            'category_id' => Category::factory(),
            'job_type_id' => JobType::factory(), // Will likely be overridden
            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::random(6),
            'description' => $this->faker->paragraphs(3, true),
            'location' => $this->faker->city() . ', ' . $this->faker->stateAbbr(),
            'salary_range' => '$' . $this->faker->numberBetween(50, 150) . 'k - $' . $this->faker->numberBetween(160, 250) . 'k',
            'status' => 'active',
        ];
    }
}
