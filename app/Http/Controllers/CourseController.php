<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use Illuminate\Http\Request;
use App\Models\Course;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'status' => ['nullable', 'integer', 'in:1,2,3'],
        ]);

        $query = Course::with('teacher:id,name')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        $courses = $query->paginate(10);

        return response()->json([
            'message' => 'Listado de cursos activos.',
            'data' => $courses,
        ], 200);
    }

    public function inactive()
    {
        $courses = Course::onlyTrashed()
            ->with('teacher')
            ->latest()
            ->paginate(10);

        return response()->json([
            'message' => 'Listado de cursos inactivos',
            'data' => $courses,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCourseRequest $request)
    {
        /* cuando necesitas crear el modelo y, 
            antes de guardarlo, agregar o modificar 
            campos que no vienen del request */

        $course = new Course($request->validated());

        $course->enrolled_count = 0;
        $course->available_seats = $course->capacity;

        $course->save();

        return response()->json([
            'message' => 'Curso creado correctamente.',
            'data' => $course->load('teacher:id,name'),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        return response()->json([
            'message' => 'Curso encontrado correctamente.',
            'data' => $course->load('teacher:id,name'),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourseRequest $request, Course $course)
    {
        $course->update($request->validated());

        return response()->json([
            'message' => 'Curso actualizado correctamente.',
            'data' => $course->load('teacher:id,name'),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        $course->delete();

        return response()->json([
            'message' => 'Curso eliminado correctamente.',
        ], 200);
    }

    public function restore($id)
    {
        $course = Course::onlyTrashed()->find($id);

        if (!$course) {
            return response()->json([
                'message' => 'Curso no encontrado o no está eliminado.',
            ], 404);
        }

        $course->restore();

        return response()->json([
            'message' => 'Curso restaurado correctamente.',
            'data' => $course->load('teacher:id,name'),
        ], 200);
    }
}
