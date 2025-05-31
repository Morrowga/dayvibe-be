<?php

namespace App\Http\Requests\Features\Category;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
            "name_mm" => ['required', 'unique:'.Category::class],
            "name_en" => ['required', 'unique:'.Category::class],
            "sizes" => ['nullable']
        ];
    }
}
