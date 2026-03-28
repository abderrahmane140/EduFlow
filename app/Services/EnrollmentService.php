<?php

namespace App\Services;

use App\Repositories\Interfaces\CourseRepositoryInterface;
use App\Repositories\Interfaces\EnrollmentRepositoryInterface;
use Exception;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class EnrollmentService
{
    public function __construct(
        private EnrollmentRepositoryInterface $enrollmentRepository,
        private CourseRepositoryInterface     $courseRepository,
        private GroupService                  $groupService,
    ) {}

    // ─── Enroll with Stripe payment ──────────────────────────
    public function enroll(int $courseId, string $paymentMethodId): array
    {
        $student = auth('api')->user();
        $course  = $this->courseRepository->findById($courseId);

        // Check if already enrolled
        $existing = $this->enrollmentRepository
            ->findByStudentAndCourse($student->id, $courseId);

        if ($existing && $existing->status === 'paid') {
            throw new Exception('You are already enrolled in this course.', 409);
        }

        // ─── Process Stripe Payment ───────────────────────────
        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $paymentIntent = PaymentIntent::create([
                'amount'               => (int) ($course->price * 100), // cents
                'currency'             => 'usd',
                'payment_method'       => $paymentMethodId,
                'confirmation_method'  => 'manual',
                'confirm'              => true,
                'return_url'           => config('app.url'),
            ]);
        } catch (\Exception $e) {
            throw new Exception('Payment failed: ' . $e->getMessage(), 402);
        }

        // ─── Save Enrollment ──────────────────────────────────
        $enrollment = $this->enrollmentRepository->create([
            'student_id'        => $student->id,
            'course_id'         => $courseId,
            'stripe_payment_id' => $paymentIntent->id,
            'status'            => 'paid',
            'enrolled_at'       => now(),
        ]);

        // ─── Auto-assign to group ─────────────────────────────
        $this->groupService->assignToGroup($courseId, $student->id);

        return [
            'message'    => 'Enrollment successful.',
            'enrollment' => $enrollment->load('course'),
        ];
    }

    // ─── Unenroll from course ─────────────────────────────────
    public function unenroll(int $courseId): array
    {
        $studentId  = auth('api')->id();
        $enrollment = $this->enrollmentRepository
            ->findByStudentAndCourse($studentId, $courseId);

        if (!$enrollment) {
            throw new Exception('You are not enrolled in this course.', 404);
        }

        $enrollment->delete();

        return ['message' => 'Successfully unenrolled from course.'];
    }

    // ─── Get student enrollments ──────────────────────────────
    public function getMyEnrollments()
    {
        return $this->enrollmentRepository
            ->getStudentEnrollments(auth('api')->id());
    }

    // ─── Get students in teacher's course ────────────────────
    public function getCourseStudents(int $courseId)
    {
        $course = $this->courseRepository->findById($courseId);

        if ($course->teacher_id !== auth('api')->id()) {
            throw new Exception('Unauthorized.', 403);
        }

        return $this->enrollmentRepository->getCourseEnrollments($courseId);
    }

    // ─── Teacher statistics ───────────────────────────────────
    public function getTeacherStats()
    {
        return $this->enrollmentRepository
            ->getTeacherStats(auth('api')->id());
    }
}