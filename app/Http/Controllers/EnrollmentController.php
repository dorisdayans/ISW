<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEnrollmentRequest;
use App\Http\Requests\UpdateEnrollmentRequest;
use App\Models\Enrollment;

class EnrollmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //$user = $request->user();
        //tabla_relacionada.id = tabla_actual.foreign_key
        $query = Enrollment::query()
            ->join('courses', 'courses.id', '=', 'enrollments.course_id')
            ->join('users as teachers', 'teachers.id', '=', 'courses.teacher_id')
            ->join('users as students', 'students.id', '=', 'enrollments.student_id')
            ->whereNull('courses.deleted_at')
            ->whereNull('teachers.deleted_at')
            ->select([
                'enrollments.id as enrollment_number',
                'students.name as student_name',
                'courses.code as course_code',
                'courses.course_name',
                'courses.start_date',
                'courses.start_time',
                'teachers.name as teacher_name',
            ]);

        $enrollments = $query
            ->orderByDesc('enrollments.id')
            ->get();

        return response()->json([
            'message' => 'Listado de matrículas.',
            'data' => $enrollments,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEnrollmentRequest $request)
    {
        
    }

    /**
     * Display the specified resource.
     */
    public function show(Enrollment $enrollment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEnrollmentRequest $request, Enrollment $enrollment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Enrollment $enrollment)
    {
        //
    }
}
