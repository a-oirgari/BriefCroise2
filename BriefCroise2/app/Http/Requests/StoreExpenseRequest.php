<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:255'],
            'amount'      => ['required', 'numeric', 'min:0.01'],
            'date'        => ['required', 'date'],
            'payer_id'    => ['required', 'exists:users,id'],
            'category_id' => ['nullable', 'exists:categories,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'    => 'Le titre de la dépense est obligatoire.',
            'title.max'         => 'Le titre ne peut pas dépasser 255 caractères.',
            'amount.required'   => 'Le montant est obligatoire.',
            'amount.numeric'    => 'Le montant doit être un nombre.',
            'amount.min'        => 'Le montant doit être supérieur à 0.',
            'date.required'     => 'La date est obligatoire.',
            'date.date'         => 'La date n\'est pas valide.',
            'payer_id.required' => 'Le payeur est obligatoire.',
            'payer_id.exists'   => 'Le payeur sélectionné n\'existe pas.',
            'category_id.exists'=> 'La catégorie sélectionnée n\'existe pas.',
        ];
    }
}