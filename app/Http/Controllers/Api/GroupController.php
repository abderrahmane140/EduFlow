<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GroupService;
use Exception;
use Illuminate\Http\JsonResponse;

class GroupController extends Controller
{
    public function __construct(
        private GroupService $groupService
    ) {}

    // GET /api/teacher/courses/{courseId}/groups
    public function courseGroups(string $courseId): JsonResponse
    {
        return response()->json(
            $this->groupService->getCourseGroups((int) $courseId)
        );
    }

    // GET /api/teacher/groups/{groupId}
    public function show(string $groupId): JsonResponse
    {
        try {
            $group = $this->groupService->getGroupWithStudents((int) $groupId);
            return response()->json($group);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }
}