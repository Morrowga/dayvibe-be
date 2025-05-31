<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderUpdateRequest extends FormRequest
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
            "msisdn" => ['required', 'string', 'size:11'],
            "address" => ['required', 'string'],
            "state" => ['required', 'string'],
            "city" => ['required', 'string'],
            "total_price" => ['required', 'numeric'],
            "total_quantity" => ['required', 'integer'],
            "content" => ['required', 'string'],
            "items" => ['required', 'array'],
            "screenshot" => ['nullable']
        ];
    }
}
