<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'name' => ['sometimes', 'required', 'string', 'min:3', 'max:100'],
            'email' => ['sometimes', 'required', 'email', 'max:150', 'unique:users,email,' . $this->user->id],
            'telephone' => ['sometimes', 'nullable', 'string', 'min:8', 'max:25'],
            'role_id' => ['sometimes', 'required', 'integer', 'exists:roles,id'],
            'password' => ['sometimes', 'required', 'string', 'min:8', 'max:72'],
        ];
    }
}
