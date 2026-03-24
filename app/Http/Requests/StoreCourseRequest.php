<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCourseRequest extends FormRequest
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
            'teacher_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('role_id', 2)
                        ->whereNull('deleted_at');
                }),
            ],
            'code' => ['required', 'string', 'min:2', 'max:50', 'unique:courses,code'],
            'course_name' => ['required', 'string', 'min:3', 'max:150'],
            'status' => ['required', 'integer'],
            'capacity' => ['required', 'integer', 'min:1'],
            'start_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
        ];
    }

    public function messages(): array
    {
        return [
            'teacher_id.exists' => 'El docente seleccionado no existe o no tiene rol de profesor.',
        ];
    }
}
