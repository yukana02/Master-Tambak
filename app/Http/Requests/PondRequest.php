<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PondRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'pond_size_notes' => ['nullable', 'string'],
            'fish_type' => ['required', 'string', 'max:255'],
            'fish_count' => ['required', 'integer', 'min:0'],
            'seed_source' => ['nullable', 'string', 'max:255'],
            'dead_fish_count' => ['nullable', 'integer', 'min:0'],
            'target_harvest_weight_kg' => ['nullable', 'numeric', 'min:0.01'],
            'stocking_date' => ['nullable', 'date'],
            'harvest_date' => ['nullable', 'date', 'after_or_equal:stocking_date'],
            'x' => ['nullable', 'integer', 'min:0'],
            'y' => ['nullable', 'integer', 'min:0'],
            'width' => ['nullable', 'integer', 'min:1', 'max:12'],
            'height' => ['nullable', 'integer', 'min:1', 'max:8'],
        ];
    }
}
