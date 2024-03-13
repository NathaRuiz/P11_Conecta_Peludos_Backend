<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnimalRequest extends FormRequest
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
            'breed' => 'required|string|max:255',
            'gender' => 'required|in:Macho,Hembra',
            'size' => 'required|in:Pequeño,Mediano,Grande,Gigante',
            'age' => 'required|in:Cachorro,Adulto,Senior',
            'approximate_age' => 'required|string|max:255',
            'status' => 'required|in:Urgente,Disponible,En Acogida,Reservado,Adoptado',
            'my_story' => 'required|string|max:500',
            'description' => 'required|string|max:400',
            'delivery_options' => 'required|string|max:255',
            'image_url' => 'required|string',
            'public_id' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'user_id' => 'required|exists:users,id',
        ];
    }
}
