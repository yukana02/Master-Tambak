<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PondFeedingRequest extends FormRequest
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
            'feed_id' => ['required', 'exists:feeds,id'],
            'fed_at' => ['required', 'date'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'unit' => ['required'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
