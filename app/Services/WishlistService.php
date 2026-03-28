<?php
// app/Services/WishlistService.php

namespace App\Services;

use App\Models\SavedCourse;
use Exception;

class WishlistService
{
    // Save a course to wishlist
    public function save(int $courseId): array
    {
        $studentId = auth('api')->id();

        $exists = SavedCourse::where('student_id', $studentId)
            ->where('course_id', $courseId)
            ->exists();

        if ($exists) {
            throw new Exception('Course already in wishlist.', 409);
        }

        SavedCourse::create([
            'student_id' => $studentId,
            'course_id'  => $courseId,
        ]);

        return ['message' => 'Course saved to wishlist.'];
    }

    // Remove a course from wishlist
    public function remove(int $courseId): array
    {
        $studentId = auth('api')->id();

        $deleted = SavedCourse::where('student_id', $studentId)
            ->where('course_id', $courseId)
            ->delete();

        if (!$deleted) {
            throw new Exception('Course not found in wishlist.', 404);
        }

        return ['message' => 'Course removed from wishlist.'];
    }

    // Get all saved courses
    public function getAll()
    {
        return auth('api')->user()
            ->savedCourses()
            ->with(['teacher:id,name,email', 'interests'])
            ->latest('saved_courses.created_at')
            ->get();
    }
}