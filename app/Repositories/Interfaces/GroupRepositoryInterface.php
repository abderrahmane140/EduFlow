<?php


namespace App\Repositories\Interfaces;

interface GroupRepositoryInterface extends RepositoryInterface
{
    public function findAvailableGroup(int $courseId);
    public function createGroupForCourse(int $courseId): mixed;
    public function assignStudentToGroup(int $groupId, int $studentId): void;
    public function getCourseGroups(int $courseId);
    public function getGroupWithStudents(int $groupId);
}