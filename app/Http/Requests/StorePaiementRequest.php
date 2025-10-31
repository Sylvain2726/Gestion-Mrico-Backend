<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaiementRequest extends FormRequest
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
            'montant_payer' => 'required|numeric|min:0',
            'mode_paiement' => ['required', Rule::in(['especes', 'orange_money', 'moov_money'])],
            'pret_id' => 'required|exists:prets,id',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'montant_payer.required' => 'Le montant à payer est requis.',
            'montant_payer.numeric' => 'Le montant doit être un nombre.',
            'montant_payer.min' => 'Le montant doit être positif.',
            'mode_paiement.required' => 'Le mode de paiement est requis.',
            'mode_paiement.in' => 'Le mode de paiement doit être: especes, orange_money ou moov_money.',
            'pret_id.required' => 'L\'ID du prêt est requis.',
            'pret_id.exists' => 'Le prêt spécifié n\'existe pas.',
        ];
    }
}
