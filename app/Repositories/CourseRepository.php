<?php
// app/Repositories/CourseRepository.php

namespace App\Repositories;

use App\Models\Course;
use App\Repositories\Interfaces\CourseRepositoryInterface;

class CourseRepository extends BaseRepository implements CourseRepositoryInterface
{
    public function __construct(Course $model)
    {
        parent::__construct($model);
    }

    // Get all courses with optional filters
    public function getAllWithFilters(array $filters = [])
    {
        $query = $this->model
            ->with(['teacher:id,name,email', 'interests']);

        // Filter by interest
        if (!empty($filters['interest_id'])) {
            $query->whereHas('interests', function ($q) use ($filters) {
                $q->where('interests.id', $filters['interest_id']);
            });
        }

        // Filter by price range
        if (!empty($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (!empty($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        // Search by title
        if (!empty($filters['search'])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%');
        }

        return $query->latest()->paginate(10);
    }

    
    public function getByTeacher(int $teacherId)
    {
        return $this->model
            ->with(['interests', 'enrollments'])
            ->where('teacher_id', $teacherId)
            ->latest()
            ->get();
    }


    public function getRecommended(array $interestIds)
    {
        return $this->model
            ->with(['teacher:id,name,email', 'interests'])
            ->whereHas('interests', function ($q) use ($interestIds) {
                $q->whereIn('interests.id', $interestIds);
            })
            ->latest()
            ->paginate(10);
    }


    public function attachInterests(int $courseId, array $interestIds): void
    {
        $course = $this->findById($courseId);
        $course->interests()->sync($interestIds);
    }
}