<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePaiementRequest extends FormRequest
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
            'montant_payer' => 'sometimes|numeric|min:0',
            'mode_paiement' => ['sometimes', Rule::in(['especes', 'orange_money', 'moov_money'])],
            'pret_id' => 'sometimes|exists:prets,id',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'montant_payer.numeric' => 'Le montant doit être un nombre.',
            'montant_payer.min' => 'Le montant doit être positif.',
            'mode_paiement.in' => 'Le mode de paiement doit être: especes, orange_money ou moov_money.',
            'pret_id.exists' => 'Le prêt spécifié n\'existe pas.',
        ];
    }
}
