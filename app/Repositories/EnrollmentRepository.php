<?php

namespace App\Repositories;

use App\Models\Enrollment;
use App\Repositories\Interfaces\EnrollmentRepositoryInterface;

class EnrollmentRepository extends BaseRepository implements EnrollmentRepositoryInterface
{
    public function __construct(Enrollment $model)
    {
        parent::__construct($model);
    }

    public function findByStudentAndCourse(int $studentId, int $courseId)
    {
        return $this->model
            ->where('student_id', $studentId)
            ->where('course_id', $courseId)
            ->first();
    }

    public function getStudentEnrollments(int $studentId)
    {
        return $this->model
            ->with(['course.teacher:id,name,email', 'course.interests'])
            ->where('student_id', $studentId)
            ->where('status', 'paid')
            ->latest()
            ->get();
    }

    public function getCourseEnrollments(int $courseId)
    {
        return $this->model
            ->with(['student:id,name,email'])
            ->where('course_id', $courseId)
            ->where('status', 'paid')
            ->latest()
            ->get();
    }

    public function getTeacherStats(int $teacherId)
    {
        return $this->model
            ->whereHas('course', function ($q) use ($teacherId) {
                $q->where('teacher_id', $teacherId);
            })
            ->with('course:id,title,price')
            ->selectRaw('course_id, count(*) as total_students, sum(courses.price) as total_revenue')
            ->join('courses', 'enrollments.course_id', '=', 'courses.id')
            ->where('enrollments.status', 'paid')
            ->groupBy('course_id')
            ->get();
    }
}