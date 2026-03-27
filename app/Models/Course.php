<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'title',
        'description',
        'price',
        'teacher_id'
    ];

    protected function casts()
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    // ─── Relationships ───────────────────────────────────────
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function interests()
    {
        return $this->belongsToMany(Interest::class, 'course_interests');
    }

    public function enrollements()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function enrolledStudents()
    {
        return $this->belongsToMany(User::class, 'enrollments', 'course_id', 'student_id')
                    ->withPivot('status', 'stripe_payment_id', 'enrolled_at');
    }

    public function savedByUsers()
    {
        return $this->belongsToMany(User::class, 'saved_courses', 'course_id', 'student_id')
                    ->withTimestamps();
    }

    public function groups()
    {
        return $this->hasMany(Group::class);
    }
}
