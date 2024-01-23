<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBlogPostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->blogPost);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:64'],
            'date' => ['required', 'date'],
            'description' => ['required', 'string', 'max:4096'],
            'slug' => ['required', 'string', 'max:255', 'unique:blog_posts,slug,'.$this->route('blogPost')->id.',id'],
        ];
    }
}
