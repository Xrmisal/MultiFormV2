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
        return [
            'first_name' => 'required|string|max:30',
            'last_name' => 'required|string|max:30',
            'email' => 'required|email|max:40',
            'phone' => 'required|phone:UK',
            'date_of_birth' => 'nullable|date|before:' . Carbon::tomorrow()->subYears(18)->toDateString(),
            'house_number' => 'nullable|integer|gt:0',
            'street_name' => 'nullable|string|max:20',
            'city' => 'nullable|string',
            'postcode' => 'nullable|postal_code:GB',
            'complete' => 'required|boolean'
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
