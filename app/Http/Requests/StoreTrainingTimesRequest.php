<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTrainingTimesRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'training_times' => ['required', 'array', 'max:5'],
            'training_times.*.hours' => ['required', 'integer', 'min:0', 'max:23'],
            'training_times.*.minutes' => ['required', 'integer', 'min:0', 'max:59'],
            'training_times.*.weekday' => ['required', 'integer', 'between:1,7', 'distinct'],
        ];
    }
}
