<?php

namespace App\Http\Requests;

use App\Models\user\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'cpf' => ['required', 'string', 'max:14', Rule::unique(User::class)->ignore($this->user()->id)],
            'cnh' => ['nullable', 'string', 'max:20'],
            'cnh_expiration_date' => ['nullable', 'date'],
            'cnh_category_id' => ['nullable', 'exists:cnh_categories,id'],
            'secretariat_id' => ['required', 'exists:secretariats,id'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
            'cnh_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'secretariat_id' => 'secretaria',
            'cnh_expiration_date' => 'data de validade da CNH',
            'cnh_category_id' => 'categoria da CNH',
            'cnh_photo' => 'foto da CNH',
        ];
    }
}
