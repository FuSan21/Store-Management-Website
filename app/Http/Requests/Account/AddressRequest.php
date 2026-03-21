<?php

namespace App\Http\Requests\Account;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'label' => ['required', 'string', 'max:40'],
            'recipient' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'regex:/^(\+?88)?01[3-9]\d{8}$/'],
            'line' => ['required', 'string', 'max:160'],
            'city' => ['required', 'string', 'max:60'],
            'is_default_shipping' => ['nullable', 'boolean'],
            'is_default_billing' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return ['phone.regex' => 'Enter a Bangladeshi mobile number, for example 01712345678.'];
    }
}
