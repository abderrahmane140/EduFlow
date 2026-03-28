<?php


namespace App\Services;

use App\Repositories\Interfaces\GroupRepositoryInterface;

class GroupService
{
    public function __construct(
        private GroupRepositoryInterface $groupRepository
    ) {}

    // Auto-assign student to a group when enrolling
    public function assignToGroup(int $courseId, int $studentId): void
    {
        // Find a group with available space
        $group = $this->groupRepository->findAvailableGroup($courseId);

        // No available group → create a new one
        if (!$group) {
            $group = $this->groupRepository->createGroupForCourse($courseId);
        }

        // Assign student
        $this->groupRepository->assignStudentToGroup($group->id, $studentId);
    }

    // Get all groups for a course (teacher)
    public function getCourseGroups(int $courseId)
    {
        return $this->groupRepository->getCourseGroups($courseId);
    }

    // Get single group with students (teacher)
    public function getGroupWithStudents(int $groupId)
    {
        return $this->groupRepository->getGroupWithStudents($groupId);
    }
}