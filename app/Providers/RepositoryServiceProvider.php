<?php


namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\UserRepository;
use App\Repositories\Interfaces\CourseRepositoryInterface;
use App\Repositories\CourseRepository;
use App\Repositories\Interfaces\EnrollmentRepositoryInterface;
use App\Repositories\EnrollmentRepository;
use App\Repositories\Interfaces\GroupRepositoryInterface;
use App\Repositories\GroupRepository;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Group;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, function ($app) {
            return new UserRepository(new User());
        });

        $this->app->bind(CourseRepositoryInterface::class, function () {
            return new CourseRepository(new Course());
        });

        $this->app->bind(EnrollmentRepositoryInterface::class, function () {
            return new EnrollmentRepository(new Enrollment());
        });

        $this->app->bind(GroupRepositoryInterface::class, function () {
            return new GroupRepository(new Group());
        });
    }
}