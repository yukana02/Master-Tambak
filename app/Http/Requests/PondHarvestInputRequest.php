<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PondHarvestInputRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'harvested_at' => ['required', 'date'],
            'bucket_name' => ['required', 'string', 'max:255'],
            'kg' => ['required', 'numeric', 'min:0.01'],
            'price_per_kg' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'harvested_at.required' => 'Tanggal panen wajib diisi.',
            'bucket_name.required' => 'Nama bakul wajib diisi.',
            'kg.required' => 'Jumlah kg wajib diisi.',
            'kg.min' => 'Jumlah kg harus lebih dari 0.',
            'price_per_kg.required' => 'Harga per kg wajib diisi.',
        ];
    }
}
