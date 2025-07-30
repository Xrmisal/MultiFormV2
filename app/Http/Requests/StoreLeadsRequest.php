<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Propaganistas\LaravelPhone;
use Axlon\PostalCodeValidation\ValidationServiceProvider;

class StoreLeadsRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:30',
            'last_name' => 'required|string|max:30',
            'email' => 'required|email|max:40|unique:leads,email',
            'phone' => 'required|phone:UK',
            'date_of_birth' => 'nullable|date|before:' . now()->subYears(18)->toDateString(),
            'house_number' => 'nullable|integer|gt:0',
            'street_name' => 'nullable|string|max:20',
            'city' => 'nullable|string',
            'postcode' => 'nullable|postal_code:GB',
            'complete' => 'required|boolean'
        ];
    }
}
