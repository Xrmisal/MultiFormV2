<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;
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

    public function rules(): array
    {
        /** @var \Carbon\Carbon $eighteenYearsAgo */
        $eighteenYearsAgo = Carbon::tomorrow()->subYears(18)->ToDateString();

        return [
            'first_name' => 'required|string|max:30',
            'last_name' => 'required|string|max:30',
            'email' => 'required|email|max:40',
            'phone' => 'required|phone:UK',
            'date_of_birth' => 'nullable|date|before:' . $eighteenYearsAgo,
            'house_number' => 'nullable|integer|gt:0',
            'street_name' => 'nullable|string|max:37',
            'city' => 'nullable|string|max:58',
            'postcode' => 'nullable|postal_code:GB',
            'proof_of_id' => 'sometimes|file|image|mimes:jpg,jpeg,png|max:10240',
            'proof_of_address' => 'sometimes|file|image|mimes:jpg,jpeg,png|max:10240',
            'status' => 'required|string'
        ];
    }
    public function messages() {
        return [
            'date_of_birth.before' => 'Must be over 18 years old to submit',
            'phone.phone' => 'Number must be a UK phone number',
            'postcode.postal_code' => 'Enter a valid UK postcode'
        ];
    }
}
