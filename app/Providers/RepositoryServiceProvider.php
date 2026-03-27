<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\UserRepository;

// use App\Repositories\Interfaces\CourseRepositoryInterface;
// use App\Repositories\CourseRepository;
// use App\Repositories\Interfaces\EnrollmentRepositoryInterface;
// use App\Repositories\EnrollmentRepository;
// use App\Repositories\Interfaces\GroupRepositoryInterface;
// use App\Repositories\GroupRepository;
// use Illuminate\Contracts\Auth\UserProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);        // $this->app->bind(CourseRepositoryInterface::class, CourseRepository::class);
        // $this->app->bind(EnrollmentRepositoryInterface::class, EnrollmentRepository::class);
        // $this->app->bind(GroupRepositoryInterface::class, GroupRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
