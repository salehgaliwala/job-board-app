# Job Board Application

A modern, full-stack Job Board application built with **Laravel 11** and **Vue.js 3** (Composition API). This platform connects job seekers with employers, offering features like job posting, searching, application management, and company profiles.

![Job Board Overview](https://via.placeholder.com/800x400?text=Job+Board+Application)

## 🚀 Features

*   **User Authentication**: Secure login and registration for Job Seekers and Employers using Laravel Sanctum.
*   **Job Listings**: Advanced search, filtering, and detailed job views.
*   **Employer Dashboard**: Manage company profiles, post new jobs, and track applications.
*   **Job Seekers**: Apply for jobs with resume uploads and track application status.
*   **Modern UI/UX**: Built with Vue 3, Tailwind CSS, and headless UI components for a responsive and accessible design.
*   **RESTful API**: Robust backend API serving data to the frontend.
*   **Advanced Employer Tools**:
    *   **Boolean Search**: Find candidates using complex keyword combinations (e.g., "PHP AND Laravel").
    *   **Smart Talent Search**: Filter and rank candidates based on skill matches.
    *   **Candidate Matching**: Automatically see best-fit candidates for each job posting.
    *   **Resume Alerts**: get notified when new candidates match your specific criteria.

## 🛠️ Technology Stack

*   **Backend**: Laravel 11, MySQL, PHP 8.2+
*   **Frontend**: Vue.js 3, Pinia (State Management), Vue Router, Tailwind CSS, Vite
*   **Authentication**: Laravel Sanctum
*   **Database**: MySQL

## 📦 Installation & Setup

### Prerequisites
*   PHP >= 8.2
*   Composer
*   Node.js & npm
*   MySQL

### 1. Clone the Repository
```bash
git clone https://github.com/YOUR_USERNAME/job-board-app.git
cd job-board-app
```

### 2. Backend Setup
```bash
cd backend
composer install
cp .env.example .env
# Configure your database credentials in .env
php artisan key:generate
php artisan migrate
php artisan serve
```

### 3. Frontend Setup
```bash
cd frontend
npm install
npm run dev
```

## 📄 License
This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---
*Built for the modern web.*
