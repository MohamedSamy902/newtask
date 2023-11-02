<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
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
            'name'                  => 'required|string|between:2,100',
            'email'                 => 'nullable|string|email|max:100|unique:users,email,'. $this->id,
            'password'              => 'required|string|min:6',
            'mobile'                => 'required|numeric|unique:users,mobile,' . $this->id,
            'status'                => 'in:active,inactive',
            'photo'                 => 'nullable|image',
            'verification_code'     => 'nullable|unique:users',
            'role'                  => 'nullable|in:admin,user',
        ];
    }
}
