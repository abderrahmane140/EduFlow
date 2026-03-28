<?php


namespace App\Repositories\Interfaces;

interface CourseRepositoryInterface extends RepositoryInterface
{
    public function getAllWithFilters(array $filters = []);
    public function getByTeacher(int $teacherId);
    public function getRecommended(array $interestIds);
    public function attachInterests(int $courseId, array $interestIds): void;
}