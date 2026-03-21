<?php

namespace App\Http\Requests;

use App\Enums\PaymentMethod;
use App\Enums\ShippingMethod;
use App\Models\Address;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * An address is either one from the customer's book (shipping_address_id)
     * or typed in at checkout (new_address.*). Billing defaults to shipping.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $ownAddress = Rule::exists('addresses', 'id')->where('user_id', $this->user()->id);

        return [
            'shipping_method' => ['required', Rule::enum(ShippingMethod::class)],
            'payment_method' => ['required', Rule::enum(PaymentMethod::class)],
            'phone' => ['required', 'string', 'regex:/^(\+?88)?01[3-9]\d{8}$/'],
            'notes' => ['nullable', 'string', 'max:500'],

            'shipping_address_id' => ['nullable', 'required_without:new_address.line', 'integer', $ownAddress],
            'new_address.recipient' => ['nullable', 'required_without:shipping_address_id', 'string', 'max:100'],
            'new_address.line' => ['nullable', 'required_without:shipping_address_id', 'string', 'max:160'],
            'new_address.city' => ['nullable', 'required_without:shipping_address_id', 'string', 'max:60'],
            'save_address' => ['nullable', 'boolean'],

            'billing_same' => ['nullable', 'boolean'],
            'billing_address_id' => ['nullable', 'integer', $ownAddress],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'Enter a Bangladeshi mobile number, for example 01712345678.',
            'shipping_address_id.required_without' => 'Choose a saved address or enter a new one.',
            'new_address.line.required_without' => 'Enter the street and area.',
            'new_address.city.required_without' => 'Enter the city.',
            'new_address.recipient.required_without' => 'Enter who will receive the parcel.',
        ];
    }

    public function shippingAddress(): string
    {
        if ($id = $this->validated('shipping_address_id')) {
            return Address::findOrFail($id)->oneLine();
        }

        return collect([
            $this->input('new_address.recipient'),
            $this->input('new_address.line'),
            $this->input('new_address.city'),
        ])->filter()->implode(', ');
    }

    public function billingAddress(): string
    {
        if (! $this->boolean('billing_same', true) && ($id = $this->validated('billing_address_id'))) {
            return Address::findOrFail($id)->oneLine();
        }

        return $this->shippingAddress();
    }
}
