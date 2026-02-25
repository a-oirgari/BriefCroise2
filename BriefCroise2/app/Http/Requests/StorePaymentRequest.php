<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payer_id'    => ['required', 'exists:users,id'],
            'receiver_id' => ['required', 'exists:users,id', 'different:payer_id'],
            'amount'      => ['required', 'numeric', 'min:0.01'],
        ];
    }

    public function messages(): array
    {
        return [
            'payer_id.required'       => 'Le payeur est obligatoire.',
            'payer_id.exists'         => 'Le payeur sélectionné n\'existe pas.',
            'receiver_id.required'    => 'Le bénéficiaire est obligatoire.',
            'receiver_id.exists'      => 'Le bénéficiaire sélectionné n\'existe pas.',
            'receiver_id.different'   => 'Le payeur et le bénéficiaire doivent être différents.',
            'amount.required'         => 'Le montant est obligatoire.',
            'amount.numeric'          => 'Le montant doit être un nombre.',
            'amount.min'              => 'Le montant doit être supérieur à 0.',
        ];
    }
}