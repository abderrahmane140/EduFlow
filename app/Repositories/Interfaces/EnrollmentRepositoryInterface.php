<?php

namespace App\Repositories\Interfaces;

interface EnrollmentRepositoryInterface extends RepositoryInterface
{
    public function findByStudentAndCourse(int $studentId, int $courseId);
    public function getStudentEnrollments(int $studentId);
    public function getCourseEnrollments(int $courseId);
    public function getTeacherStats(int $teacherId);
}