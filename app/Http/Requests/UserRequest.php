<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'address' => 'required|string|max:255',
            'province_id' => 'required|exists:provinces,id',
            'description' => 'nullable|string|max:400',
            'telephone' => 'required|string|max:20',
            'image_url' => 'nullable',
            'public_id' => 'nullable',
            'password' => 'required|string|min:6|confirmed',
        ];
    }
}
