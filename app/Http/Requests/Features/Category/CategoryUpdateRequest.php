<?php

namespace App\Http\Requests\Features\Category;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CategoryUpdateRequest extends FormRequest
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
        $categoryId = $this->route('category');

        return [
            "name_mm" => [
                'required',
                Rule::unique('categories')->ignore($categoryId),
            ],
            "name_en" => [
                'required',
                Rule::unique('categories')->ignore($categoryId),
            ],
            "sizes" => ['nullable']
        ];
    }
}
