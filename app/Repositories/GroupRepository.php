<?php
// app/Repositories/GroupRepository.php

namespace App\Repositories;

use App\Models\Group;
use App\Repositories\Interfaces\GroupRepositoryInterface;

class GroupRepository extends BaseRepository implements GroupRepositoryInterface
{
    public function __construct(Group $model)
    {
        parent::__construct($model);
    }

    // Find a group that still has space
    public function findAvailableGroup(int $courseId)
    {
        return Group::where('course_id', $courseId)
            ->withCount('students')
            ->having('students_count', '<', 25)
            ->first();
    }

    // Create a new group for a course
    public function createGroupForCourse(int $courseId): mixed
    {
        // Count existing groups to name the new one
        $groupCount = Group::where('course_id', $courseId)->count();
        $groupName  = 'Group ' . chr(65 + $groupCount); // Group A, Group B...

        return Group::create([
            'course_id'    => $courseId,
            'name'         => $groupName,
            'max_students' => 25,
        ]);
    }

    // Add student to group
    public function assignStudentToGroup(int $groupId, int $studentId): void
    {
        $group = $this->findById($groupId);
        $group->students()->attach($studentId);
    }

    // Get all groups for a course
    public function getCourseGroups(int $courseId)
    {
        return Group::where('course_id', $courseId)
            ->withCount('students')
            ->with('students:id,name,email')
            ->get();
    }

    // Get single group with students
    public function getGroupWithStudents(int $groupId)
    {
        return Group::with('students:id,name,email')
            ->withCount('students')
            ->findOrFail($groupId);
    }
}