<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;
use Propaganistas\LaravelPhone;
use Axlon\PostalCodeValidation\ValidationServiceProvider;

class UpdateLeadRequest extends FormRequest
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
        $eighteenYearsAgo = Carbon::tomorrow()->subYears(18)->ToDateString();
        return [
            'first_name' => 'sometimes|string|max:30',
            'last_name' => 'sometimes|string|max:30',
            'email' => 'sometimes|email|max:40',
            'phone' => 'sometimes|phone:UK',
            'date_of_birth' => 'sometimes|date|before:' . $eighteenYearsAgo,
            'house_number' => 'sometimes|integer|gt:0',
            'street_name' => 'sometimes|string|max:37',
            'city' => 'sometimes|string|max:58',
            'postcode' => 'sometimes|postal_code:GB',
            'proof_of_id' => 'sometimes|file|image|mimes:jpg,jpeg,png|max:10240',
            'proof_of_address' => 'sometimes|file|image|mimes:jpg,jpeg,png|max:10240',
            'status' => 'sometimes|string'
        ];
    }
}
