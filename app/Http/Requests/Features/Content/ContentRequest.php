<?php

namespace App\Http\Requests\Features\Content;

use Illuminate\Foundation\Http\FormRequest;

class ContentRequest extends FormRequest
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
            "title" => ['required'],
            "content" => ['required'],
            "slug" => ['required'],
            "description" => ['required'],
            "display_url" => ['required', 'unique:contents,display_url'],
            "brand_id" => ['required']
        ];
    }
}
