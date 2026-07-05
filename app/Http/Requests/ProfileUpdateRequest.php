<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'username' => [
                'sometimes', 'required', 'string', 'max:50',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'phone'    => [
                'sometimes', 'required', 'string', 'max:20',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'position' => ['sometimes', 'required', 'in:GK,DF,MF,FW'],
            'email'    => [
                'nullable', 'string', 'email', 'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
        ];
    }
}
