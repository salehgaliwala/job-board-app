<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\JobListing;
use App\Models\Category;
use App\Models\JobType;
use App\Models\Company;
use App\Models\Skill;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Job Types
        $types = ['Full-time', 'Part-time', 'Contract', 'Freelance', 'Remote'];
        $jobTypes = collect($types)->map(function ($name) {
            return JobType::create([
                'name' => $name,
                'slug' => Str::slug($name),
            ]);
        });

        // 2. Categories
        $categoryNames = ['Engineering', 'Design', 'Marketing', 'Sales', 'Product', 'Customer Support', 'Finance', 'HR'];
        $categories = collect($categoryNames)->map(function ($name) {
            return Category::create([
                'name' => $name,
                'slug' => Str::slug($name),
            ]);
        });

        // 3. Create a Test User (Employer)
        $user = User::factory()->create([
            'name' => 'Test Employer',
            'email' => 'employer@example.com',
            'role' => 'employer',
            'password' => bcrypt('password'),
        ]);

        $company = Company::factory()->create([
            'user_id' => $user->id,
            'name' => 'Tech Corp',
            'slug' => 'tech-corp',
        ]);

        // 4. Create Companies and Jobs
        // Create 20 random companies
        $companies = Company::factory(20)->create();

        // Create 100 Jobs
        foreach ($companies as $comp) {
            JobListing::factory(rand(2, 6))->create([
                'company_id' => $comp->id,
                'category_id' => $categories->random()->id,
                'job_type_id' => $jobTypes->random()->id,
            ]);
        }

        // Add some jobs for the test company
        JobListing::factory(5)->create([
            'company_id' => $company->id,
            'category_id' => $categories->random()->id,
            'job_type_id' => $jobTypes->random()->id,
        ]);
        // 5. Skills
        $skillNames = ['PHP', 'Laravel', 'Vue.js', 'React', 'JavaScript', 'HTML', 'CSS', 'Tailwind', 'MySQL', 'Node.js', 'Python', 'Django', 'Git', 'Docker', 'AWS'];
        $skills = collect($skillNames)->map(function ($name) {
            return Skill::create(['name' => $name]);
        });

        // 6. Seekers with Skills
        $seekers = User::factory(10)->create([
            'role' => 'seeker',
        ]);

        foreach ($seekers as $seeker) {
            $seeker->skills()->attach($skills->random(rand(2, 5))->pluck('id'));
        }

        // Test Seeker
        $testSeeker = User::factory()->create([
            'name' => 'Test Seeker',
            'email' => 'seeker@example.com',
            'role' => 'seeker',
            'password' => bcrypt('password'),
        ]);
        $testSeeker->skills()->attach($skills->whereIn('name', ['PHP', 'Laravel', 'Vue.js'])->pluck('id'));

        // 7. Attach Skills to Jobs
        $allJobs = JobListing::all();
        foreach ($allJobs as $job) {
            $job->skills()->attach($skills->random(rand(1, 4))->pluck('id'));
        }
    }
}
