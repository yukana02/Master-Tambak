<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class FeedRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'composition' => ['nullable', 'string'],
            'sack_weight_kg' => ['required', 'numeric', 'min:0.01'],
            'fcr' => ['required', 'numeric', 'min:0.01'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
