<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCourseRequest extends FormRequest
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
        $courseId = $this->route('course')->id;

        return [
            'teacher_id' => [
                'sometimes',
                'integer',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('role_id', 2)
                        ->whereNull('deleted_at');
                }),
            ],
            'code' => [
                'sometimes',
                'string',
                'min:2',
                'max:50',
                Rule::unique('courses', 'code')->ignore($courseId),
            ],
            'course_name' => ['sometimes', 'string', 'min:3', 'max:150'],
            'status' => ['sometimes', 'integer'],
            'capacity' => ['sometimes', 'integer', 'min:1'],
            'start_date' => ['sometimes', 'date'],
            'start_time' => ['sometimes', 'date_format:H:i'],
        ];
    }

    public function messages(): array
    {
        return [
            'teacher_id.integer' => 'El identificador del docente debe ser numérico.',
            'teacher_id.exists' => 'El docente seleccionado no existe o no tiene rol de profesor.',
            'code.unique' => 'El código del curso ya está registrado.',
        ];
    }
}
