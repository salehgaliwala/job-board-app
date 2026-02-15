<x-mail::message>
    # New Job Application Received

    Hello {{ $application->job->company->user->name }},

    You have received a new application for the position of **{{ $application->job->title }}**.

    **Applicant Details:**
    - **Name:** {{ $application->user->name }}
    - **Email:** {{ $application->user->email }}

    **Cover Letter:**
    {{ $application->cover_letter ?? 'No cover letter provided.' }}

    You can view the full application and resume in your dashboard.

    <x-mail::button :url="config('app.frontend_url') . '/employer/applications'">
        View Applications
    </x-mail::button>

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>