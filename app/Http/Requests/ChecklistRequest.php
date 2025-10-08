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
            'checklist.*.status' => 'required|in:ok,problem,not_applicable',
            'checklist.*.notes' => 'nullable|string|max:500',
            'general_notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'checklist.required' => 'Você deve preencher o checklist.',
            'checklist.*.status.required' => 'Todos os itens do checklist devem ter um status.',
            'checklist.*.status.in' => 'Status inválido. Escolha: OK, Problema ou N/A.',
        ];
    }
}
