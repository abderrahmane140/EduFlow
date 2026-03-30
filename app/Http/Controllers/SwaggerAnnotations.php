<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'EduFlow API',
    version: '1.0.0',
    description: 'School Management API Documentation'
)]
#[OA\Server(
    url: 'http://127.0.0.1:8000',
    description: 'Local Development Server'
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT'
)]

// ─── AUTH ─────────────────────────────────────────────────

#[OA\Post(
    path: '/api/auth/register',
    summary: 'Register a new user',
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['name', 'email', 'password', 'password_confirmation', 'role'],
            properties: [
                new OA\Property(property: 'name',     type: 'string',  example: 'John Doe'),
                new OA\Property(property: 'email',    type: 'string',  example: 'john@example.com'),
                new OA\Property(property: 'password', type: 'string',  example: 'password123'),
                new OA\Property(property: 'password_confirmation', type: 'string', example: 'password123'),
                new OA\Property(property: 'role',     type: 'string',  enum: ['student', 'teacher']),
            ]
        )
    ),
    tags: ['Authentication'],
    responses: [
        new OA\Response(response: 201, description: 'Registered successfully'),
        new OA\Response(response: 422, description: 'Validation error'),
    ]
)]
#[OA\Post(
    path: '/api/auth/login',
    summary: 'Login user',
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['email', 'password'],
            properties: [
                new OA\Property(property: 'email',    type: 'string', example: 'john@example.com'),
                new OA\Property(property: 'password', type: 'string', example: 'password123'),
            ]
        )
    ),
    tags: ['Authentication'],
    responses: [
        new OA\Response(response: 200, description: 'Login successful'),
        new OA\Response(response: 401, description: 'Invalid credentials'),
    ]
)]
#[OA\Post(
    path: '/api/auth/logout',
    summary: 'Logout user',
    security: [['bearerAuth' => []]],
    tags: ['Authentication'],
    responses: [new OA\Response(response: 200, description: 'Logged out')]
)]
#[OA\Post(
    path: '/api/auth/refresh',
    summary: 'Refresh JWT token',
    security: [['bearerAuth' => []]],
    tags: ['Authentication'],
    responses: [new OA\Response(response: 200, description: 'Token refreshed')]
)]
#[OA\Get(
    path: '/api/auth/me',
    summary: 'Get authenticated user',
    security: [['bearerAuth' => []]],
    tags: ['Authentication'],
    responses: [new OA\Response(response: 200, description: 'Authenticated user')]
)]

// ─── COURSES ──────────────────────────────────────────────

#[OA\Get(
    path: '/api/courses',
    summary: 'Get all courses',
    tags: ['Courses'],
    parameters: [
        new OA\Parameter(name: 'search',      in: 'query', schema: new OA\Schema(type: 'string')),
        new OA\Parameter(name: 'interest_id', in: 'query', schema: new OA\Schema(type: 'integer')),
        new OA\Parameter(name: 'min_price',   in: 'query', schema: new OA\Schema(type: 'number')),
        new OA\Parameter(name: 'max_price',   in: 'query', schema: new OA\Schema(type: 'number')),
    ],
    responses: [new OA\Response(response: 200, description: 'List of courses')]
)]
#[OA\Get(
    path: '/api/courses/recommended',
    summary: 'Get recommended courses',
    security: [['bearerAuth' => []]],
    tags: ['Courses'],
    responses: [new OA\Response(response: 200, description: 'Recommended courses')]
)]
#[OA\Get(
    path: '/api/courses/{id}',
    summary: 'Get course details',
    tags: ['Courses'],
    parameters: [
        new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ],
    responses: [
        new OA\Response(response: 200, description: 'Course details'),
        new OA\Response(response: 404, description: 'Not found'),
    ]
)]
#[OA\Get(
    path: '/api/teacher/courses',
    summary: "Get teacher's own courses",
    security: [['bearerAuth' => []]],
    tags: ['Courses'],
    responses: [new OA\Response(response: 200, description: 'Teacher courses')]
)]
#[OA\Post(
    path: '/api/teacher/courses',
    summary: 'Create a course',
    security: [['bearerAuth' => []]],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['title', 'price'],
            properties: [
                new OA\Property(property: 'title',       type: 'string', example: 'Laravel Masterclass'),
                new OA\Property(property: 'description', type: 'string', example: 'Learn Laravel'),
                new OA\Property(property: 'price',       type: 'number', example: 49.99),
            ]
        )
    ),
    tags: ['Courses'],
    responses: [
        new OA\Response(response: 201, description: 'Course created'),
        new OA\Response(response: 403, description: 'Unauthorized'),
    ]
)]
#[OA\Put(
    path: '/api/teacher/courses/{id}',
    summary: 'Update a course',
    security: [['bearerAuth' => []]],
    tags: ['Courses'],
    parameters: [
        new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ],
    responses: [new OA\Response(response: 200, description: 'Course updated')]
)]
#[OA\Delete(
    path: '/api/teacher/courses/{id}',
    summary: 'Delete a course',
    security: [['bearerAuth' => []]],
    tags: ['Courses'],
    parameters: [
        new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ],
    responses: [new OA\Response(response: 200, description: 'Course deleted')]
)]

// ─── WISHLIST ─────────────────────────────────────────────

#[OA\Get(
    path: '/api/student/wishlist',
    summary: 'Get student wishlist',
    security: [['bearerAuth' => []]],
    tags: ['Wishlist'],
    responses: [new OA\Response(response: 200, description: 'Wishlist')]
)]
#[OA\Post(
    path: '/api/student/wishlist/{courseId}',
    summary: 'Save course to wishlist',
    security: [['bearerAuth' => []]],
    tags: ['Wishlist'],
    parameters: [
        new OA\Parameter(name: 'courseId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ],
    responses: [
        new OA\Response(response: 201, description: 'Saved to wishlist'),
        new OA\Response(response: 409, description: 'Already in wishlist'),
    ]
)]
#[OA\Delete(
    path: '/api/student/wishlist/{courseId}',
    summary: 'Remove from wishlist',
    security: [['bearerAuth' => []]],
    tags: ['Wishlist'],
    parameters: [
        new OA\Parameter(name: 'courseId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ],
    responses: [new OA\Response(response: 200, description: 'Removed from wishlist')]
)]

// ─── ENROLLMENTS ──────────────────────────────────────────

#[OA\Post(
    path: '/api/student/enroll',
    summary: 'Enroll in a course with Stripe payment',
    security: [['bearerAuth' => []]],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['course_id', 'payment_method_id'],
            properties: [
                new OA\Property(property: 'course_id',         type: 'integer', example: 1),
                new OA\Property(property: 'payment_method_id', type: 'string',  example: 'pm_card_visa'),
            ]
        )
    ),
    tags: ['Enrollments'],
    responses: [
        new OA\Response(response: 201, description: 'Enrolled successfully'),
        new OA\Response(response: 402, description: 'Payment failed'),
        new OA\Response(response: 409, description: 'Already enrolled'),
    ]
)]
#[OA\Delete(
    path: '/api/student/unenroll/{courseId}',
    summary: 'Unenroll from a course',
    security: [['bearerAuth' => []]],
    tags: ['Enrollments'],
    parameters: [
        new OA\Parameter(name: 'courseId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ],
    responses: [new OA\Response(response: 200, description: 'Unenrolled')]
)]
#[OA\Get(
    path: '/api/student/enrollments',
    summary: 'Get my enrollments',
    security: [['bearerAuth' => []]],
    tags: ['Enrollments'],
    responses: [new OA\Response(response: 200, description: 'Enrollments list')]
)]

// ─── TEACHER ──────────────────────────────────────────────

#[OA\Get(
    path: '/api/teacher/courses/{courseId}/students',
    summary: 'Get students in a course',
    security: [['bearerAuth' => []]],
    tags: ['Teacher'],
    parameters: [
        new OA\Parameter(name: 'courseId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ],
    responses: [new OA\Response(response: 200, description: 'Students list')]
)]
#[OA\Get(
    path: '/api/teacher/stats',
    summary: 'Get teacher statistics',
    security: [['bearerAuth' => []]],
    tags: ['Teacher'],
    responses: [new OA\Response(response: 200, description: 'Statistics')]
)]

// ─── GROUPS ───────────────────────────────────────────────

#[OA\Get(
    path: '/api/teacher/courses/{courseId}/groups',
    summary: 'Get course groups',
    security: [['bearerAuth' => []]],
    tags: ['Groups'],
    parameters: [
        new OA\Parameter(name: 'courseId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ],
    responses: [new OA\Response(response: 200, description: 'Groups list')]
)]
#[OA\Get(
    path: '/api/teacher/groups/{groupId}',
    summary: 'Get group with students',
    security: [['bearerAuth' => []]],
    tags: ['Groups'],
    parameters: [
        new OA\Parameter(name: 'groupId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ],
    responses: [new OA\Response(response: 200, description: 'Group details')]
)]
class SwaggerAnnotations {}