<?php

namespace App\Http\Requests;

use App\Models\Training;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TrainingTimesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'hours' => [
                'required',
                'unsignedTinyInteger',
                'min:0',
                'max:23'
            ],
            'minutes' => [
                'required',
                'unsignedTinyInteger',
                'min:0',
                'max:59'],
            'weekday' => [
                'required', 
                'unsignedTinyInteger',
                'min:0',
                'max:7',
                Rule::unique('training', 'weekday')->where(function ($query) {
                    return $query->where('weekday', request('weekday'));
                })
            ]
        ];
    }
}
