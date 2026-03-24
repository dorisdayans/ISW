<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use App\Models\Course;

class StoreEnrollmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'student_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')
                    ->where(fn($query) => $query
                        ->where('role_id', 3)
                        ->whereNull('deleted_at')),
            ],

            'course_id' => [
                'required',
                'integer',
                Rule::exists('courses', 'id')
                    ->where(fn($query) => $query
                        ->where('status', 1)
                        ->whereNull('deleted_at')),
                Rule::unique('enrollments', 'course_id')
                    ->where(fn($query) => $query
                        ->where('student_id', $this->input('student_id')))
                    ->withoutTrashed(),
            ],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $courseId = $this->input('course_id');

                if (! $courseId) {
                    return;
                }

                $course = Course::query()
                    ->where('id', $courseId)
                    ->where('status', 1)
                    ->whereNull('deleted_at')
                    ->first();

                if (! $course) {
                    return;
                }

                if ((int) $course->available_seats <= 0) {
                    $validator->errors()->add(
                        'course_id',
                        'El curso seleccionado no tiene cupos disponibles.'
                    );
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'student_id.required' => 'El campo estudiante es obligatorio.',
            'student_id.integer'  => 'El campo estudiante debe ser un número entero.',
            'student_id.exists'   => 'El estudiante seleccionado no existe, no está activo o no tiene rol de estudiante.',

            'course_id.required' => 'El campo curso es obligatorio.',
            'course_id.integer'  => 'El campo curso debe ser un número entero.',
            'course_id.exists'   => 'El curso seleccionado no existe, no está activo o no se encuentra en estado "Por iniciar".',
            'course_id.unique'   => 'El estudiante ya tiene una matrícula activa en este curso.',
        ];
    }

    public function attributes(): array
    {
        return [
            'student_id' => 'estudiante',
            'course_id'  => 'curso',
        ];
    }
}
