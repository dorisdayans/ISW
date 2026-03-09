<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enrollment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'student_id',
        'course_id',
    ];

    /**
     * Relación: esta matrícula pertenece a un estudiante (User).
     * enrollments.student_id -> users.id
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id', 'id');
    }

    /**
     * Relación: esta matrícula pertenece a un curso.
     * enrollments.course_id -> courses.id
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'id');
    }
}
