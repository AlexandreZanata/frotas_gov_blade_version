<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChecklistRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'checklist' => 'required|array',
            'checklist.*.status' => 'required|in:ok,attention,problem',
            'checklist.*.notes' => 'nullable|string|max:500',
            'general_notes' => 'nullable|string|max:1000',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $checklist = $this->input('checklist', []);

            foreach ($checklist as $itemId => $data) {
                // Se o status for "problem", a descrição é obrigatória
                if (isset($data['status']) && $data['status'] === 'problem') {
                    if (empty($data['notes']) || trim($data['notes']) === '') {
                        $validator->errors()->add(
                            "checklist.{$itemId}.notes",
                            'A descrição do problema é obrigatória quando o status for "Problema".'
                        );
                    }
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'checklist.required' => 'Você deve preencher o checklist.',
            'checklist.*.status.required' => 'Todos os itens do checklist devem ter um status.',
            'checklist.*.status.in' => 'Status inválido. Escolha: OK, Atenção ou Problema.',
            'checklist.*.notes.max' => 'A descrição não pode ter mais de 500 caracteres.',
            'general_notes.max' => 'As observações gerais não podem ter mais de 1000 caracteres.',
        ];
    }
}
