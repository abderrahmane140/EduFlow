<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\EnrollmentService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function __construct(
        private EnrollmentService $enrollmentService
    ) {}

    // POST /api/student/enroll
    public function enroll(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'course_id'         => 'required|exists:courses,id',
            'payment_method_id' => 'required|string',
        ]);

        try {
            $result = $this->enrollmentService->enroll(
                (int) $validated['course_id'],
                $validated['payment_method_id']
            );
            return response()->json($result, 201);
        } catch (Exception $e) {
            return response()->json(
                ['message' => $e->getMessage()],
                (int) $e->getCode() ?: 500
            );
        }
    }

    // DELETE /api/student/unenroll/{courseId}
    public function unenroll(string $courseId): JsonResponse
    {
        try {
            $result = $this->enrollmentService->unenroll((int) $courseId);
            return response()->json($result);
        } catch (Exception $e) {
            return response()->json(
                ['message' => $e->getMessage()],
                (int) $e->getCode() ?: 500
            );
        }
    }

    // GET /api/student/enrollments
    public function myEnrollments(): JsonResponse
    {
        return response()->json($this->enrollmentService->getMyEnrollments());
    }

    // GET /api/teacher/courses/{courseId}/students
    public function courseStudents(string $courseId): JsonResponse
    {
        try {
            $result = $this->enrollmentService->getCourseStudents((int) $courseId);
            return response()->json($result);
        } catch (Exception $e) {
            return response()->json(
                ['message' => $e->getMessage()],
                (int) $e->getCode() ?: 500
            );
        }
    }

    // GET /api/teacher/stats
    public function teacherStats(): JsonResponse
    {
        return response()->json($this->enrollmentService->getTeacherStats());
    }
}