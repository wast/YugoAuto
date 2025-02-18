<?php

namespace App\Source\Ride\App\Requests;

use App\Enum\TimeEnum;
use Illuminate\Foundation\Http\FormRequest;

class SearchRidesRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'from_place_id' => ['nullable', 'integer'],
            'to_place_id' => ['nullable', 'integer'],
            'min_time' => ['nullable', 'date_format:' . TimeEnum::DATE_FORMAT->value],
            'filters' => ['nullable', 'array']
        ];
    }
}
