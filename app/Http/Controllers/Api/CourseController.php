<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CourseService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function __construct(
        private CourseService $courseService
    ) {}

    // GET /api/courses
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'interest_id', 'min_price', 'max_price']);
        return response()->json($this->courseService->getAllCourses($filters));
    }

    // GET /api/courses/{id}
    public function show(string $id): JsonResponse  // ← string not int
    {
        try {
            $course = $this->courseService->getCourse((int) $id);  // ← cast here
            return response()->json($course);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }

    // GET /api/courses/recommended
    public function recommended(): JsonResponse
    {
        return response()->json($this->courseService->getRecommended());
    }

    // GET /api/teacher/courses
    public function teacherCourses(): JsonResponse
    {
        return response()->json($this->courseService->getTeacherCourses());
    }

    // POST /api/teacher/courses
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'price'          => 'required|numeric|min:0',
            'interest_ids'   => 'array',
            'interest_ids.*' => 'exists:interests,id',
        ]);

        try {
            $course = $this->courseService->createCourse($validated);
            return response()->json($course, 201);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    // PUT /api/teacher/courses/{id}
    public function update(Request $request, string $id): JsonResponse  // ← string not int
    {
        $validated = $request->validate([
            'title'          => 'sometimes|string|max:255',
            'description'    => 'nullable|string',
            'price'          => 'sometimes|numeric|min:0',
            'interest_ids'   => 'array',
            'interest_ids.*' => 'exists:interests,id',
        ]);

        try {
            $course = $this->courseService->updateCourse((int) $id, $validated);  // ← cast here
            return response()->json($course);
        } catch (Exception $e) {
            return response()->json(
                ['message' => $e->getMessage()],
                (int) $e->getCode() ?: 500
            );
        }
    }

    // DELETE /api/teacher/courses/{id}
    public function destroy(string $id): JsonResponse  // ← string not int
    {
        try {
            $this->courseService->deleteCourse((int) $id);  // ← cast here
            return response()->json(['message' => 'Course deleted successfully']);
        } catch (Exception $e) {
            return response()->json(
                ['message' => $e->getMessage()],
                (int) $e->getCode() ?: 500
            );
        }
    }
}