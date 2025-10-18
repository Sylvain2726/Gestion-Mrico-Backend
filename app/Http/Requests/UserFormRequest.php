<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UserFormRequest extends FormRequest
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
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->user->id)],
            'password' => 'required|string|min:8|confirmed',
            'tel' => ['required', 'string', 'max:255', Rule::unique('users', 'tel')->ignore($this->user->id)],
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {

        throw new HttpResponseException(
            response()->json([
                'message' => 'Erreur de validation',
                'errors' => $validator->errors(),
            ], 422)
        );
    }

    //Message de validation 
    public function messages(): array
    {
        return [
            'name.required' => 'Le nom est requis',
            'email.required' => 'L\'email est requis',
            'email.email' => 'L\'email doit être valide',
            'email.unique' => 'Cet email est déjà utilisé',
            'password.required' => 'Le mot de passe est requis',
            'password.min' => 'Le mot de passe doit faire au moins 8 caractères',
            'password.confirmed' => 'Les mots de passe ne correspondent pas',
            'tel.required' => 'Le numéro de téléphone est requis',
            'tel.unique' => 'Ce numéro de téléphone est déjà utilisé',
        ];
    }
}
