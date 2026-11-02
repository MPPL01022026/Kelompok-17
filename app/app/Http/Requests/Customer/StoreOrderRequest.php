<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'min:2', 'max:120'],
            'phone' => ['required', 'string', 'regex:/^(\+62|62|0)8[1-9][0-9]{6,12}$/'],
            'note' => ['nullable', 'string', 'max:500'],
            'payment_method' => ['required', 'in:cod,whatsapp,tunai,transfer'],
            'items' => ['required', 'array', 'min:1', 'max:30'],
            'items.*' => ['integer', 'min:1', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required' => 'Nama pemesan wajib diisi.',
            'customer_name.min' => 'Nama pemesan minimal 2 karakter.',
            'phone.required' => 'Nomor WhatsApp wajib diisi.',
            'phone.regex' => 'Format nomor WhatsApp tidak valid (gunakan 08xx atau 628xx).',
            'items.required' => 'Keranjang masih kosong.',
            'items.min' => 'Keranjang masih kosong.',
            'items.*.min' => 'Jumlah item minimal 1.',
            'items.*.max' => 'Jumlah per item maksimal 100.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'customer_name' => $this->string('customer_name')->trim()->toString(),
            'phone' => preg_replace('/[\s\-]+/', '', $this->string('phone')->toString()),
        ]);
    }
}
