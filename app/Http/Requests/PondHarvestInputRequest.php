<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

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
            'payment_method' => ['nullable', Rule::in(['cash', 'tf', 'split'])],
            'cash_amount' => ['nullable', 'numeric', 'min:0'],
            'tf_amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $total = (float) $this->input('kg', 0) * (float) $this->input('price_per_kg', 0);
        $method = $this->input('payment_method');
        $cash = (float) $this->input('cash_amount', 0);
        $tf = (float) $this->input('tf_amount', 0);

        if ($method === null || $method === '') {
            $method = 'cash';
            $this->merge(['payment_method' => $method]);
        }

        if ($method === 'cash') {
            $cash = $total;
            $tf = 0;
        } elseif ($method === 'tf') {
            $cash = 0;
            $tf = $total;
        } elseif ($method === 'split') {
            $cash = min(max($cash, 0), $total);
            $tf = max($total - $cash, 0);
        }

        $this->merge([
            'payment_method' => $method,
            'cash_amount' => $cash,
            'tf_amount' => $tf,
        ]);
    }

    public function passedValidation(): void
    {
        $total = (float) $this->input('kg', 0) * (float) $this->input('price_per_kg', 0);
        $method = $this->input('payment_method');
        $cash = (float) $this->input('cash_amount', 0);
        $tf = (float) $this->input('tf_amount', 0);

        if (! in_array($method, ['cash', 'tf', 'split'], true)) {
            throw ValidationException::withMessages([
                'payment_method' => 'Payment method tidak valid.',
            ]);
        }

        if ($method === 'split' && abs(($cash + $tf) - $total) > 0.01) {
            throw ValidationException::withMessages([
                'cash_amount' => 'Total Cash dan TF harus sama dengan total transaksi.',
            ]);
        }
    }
}
