<?php
// app/Services/CourseService.php

namespace App\Services;

use App\Repositories\Interfaces\CourseRepositoryInterface;
use Exception;

class CourseService
{
    public function __construct(
        private CourseRepositoryInterface $courseRepository
    ) {}

    // ─── Get all courses (student) ───────────────────────────
    public function getAllCourses(array $filters = [])
    {
        return $this->courseRepository->getAllWithFilters($filters);
    }

    // ─── Get single course ───────────────────────────────────
    public function getCourse(int $id)
    {
        $course = $this->courseRepository->findById($id);
        return $course->load(['teacher:id,name,email', 'interests']);
    }

    // ─── Get recommended courses (student) ──────────────────
    public function getRecommended()
    {
        $user        = auth('api')->user();
        $interestIds = $user->interests()->pluck('interests.id')->toArray();

        if (empty($interestIds)) {
            return $this->courseRepository->getAllWithFilters();
        }

        return $this->courseRepository->getRecommended($interestIds);
    }

    // ─── Create course (teacher) ─────────────────────────────
    public function createCourse(array $data)
    {
        $interestIds = $data['interest_ids'] ?? [];
        unset($data['interest_ids']);

        $data['teacher_id'] = auth('api')->id();

        $course = $this->courseRepository->create($data);

        if (!empty($interestIds)) {
            $this->courseRepository->attachInterests($course->id, $interestIds);
        }

        return $course->load(['teacher:id,name,email', 'interests']);
    }

    // ─── Update course (teacher) ─────────────────────────────
    public function updateCourse(int $id, array $data)
    {
        $course = $this->courseRepository->findById($id);

        // Make sure only the owner can update
        if ($course->teacher_id !== auth('api')->id()) {
            throw new Exception('Unauthorized. You do not own this course.', 403);
        }

        $interestIds = $data['interest_ids'] ?? null;
        unset($data['interest_ids']);

        $course = $this->courseRepository->update($id, $data);

        if (!is_null($interestIds)) {
            $this->courseRepository->attachInterests($course->id, $interestIds);
        }

        return $course->load(['teacher:id,name,email', 'interests']);
    }

    // ─── Delete course (teacher) ─────────────────────────────
    public function deleteCourse(int $id): void
    {
        $course = $this->courseRepository->findById($id);

        // Make sure only the owner can delete
        if ($course->teacher_id !== auth('api')->id()) {
            throw new Exception('Unauthorized. You do not own this course.', 403);
        }

        $this->courseRepository->delete($id);
    }

    // ─── Get teacher's own courses ───────────────────────────
    public function getTeacherCourses()
    {
        return $this->courseRepository->getByTeacher(auth('api')->id());
    }
}