<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'email|unique:users,email',
            'document' => [
                'nullable',
                'cpf_cnpj',
                Rule::unique('users', 'document')->ignore($this->user),
            ],
            'type' => 'required|in:client,seller',
            'wallet' => 'required|numeric',
            'password' => 'required',
        ];
    }
}
