<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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
            "name" => ['required', 'string'],
           "msisdn" => [
                'required',
                'string', // Ensures it's a string
                function ($attribute, $value, $fail) {
                    // Ensure the msisdn starts with '09'
                    if (substr($value, 0, 2) !== '09') {
                        $fail('The ' . $attribute . ' must start with 09.');
                    }

                    // If msisdn starts with '095', it must be exactly 7 digits long
                    if (substr($value, 0, 3) === '095' && strlen($value) !== 9) {
                        $fail('The ' . $attribute . ' must be 9 digits long when starting with 095.');
                    }

                    // If msisdn starts with '09' but not '095', it must be exactly 11 digits long
                    if (substr($value, 0, 2) === '09' && substr($value, 0, 3) !== '095' && strlen($value) !== 11) {
                        $fail('The ' . $attribute . ' must be 11 digits long when starting with 09 (but not 095).');
                    }
                },
            ],
            "address" => ['required', 'string'],
            "state" => ['required', 'string'],
            "city" => ['required', 'string'],
            "total_price" => ['required', 'numeric'],
            "total_quantity" => ['required', 'integer'],
            "content" => ['nullable'],
            // "items" => ['required', 'array'],
            "screenshot" => ['required', 'file', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];
    }
}
