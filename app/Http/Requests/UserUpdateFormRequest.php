<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateFormRequest extends FormRequest
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
            'name' => 'string|max:255',
            'email' => ['string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->user->id)],
            'tel' => ['string', 'max:255', Rule::unique('users', 'tel')->ignore($this->user->id)],
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'Le nom doit être une chaîne de caractères',
            'email.email' => 'L\'email doit être valide',
            'email.unique' => 'L\'email est déjà utilisé',
            'tel.unique' => 'Le numéro de téléphone est déjà utilisé',
        ];
    }
}
