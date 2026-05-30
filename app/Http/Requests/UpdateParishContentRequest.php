<?php

namespace App\Http\Requests;

use App\Models\ParishContent;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateParishContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $contentId = (int) $this->route('id');
        $content = ParishContent::query()->find($contentId);
        $keyRules = [
            'required',
            'string',
            'max:100',
            'regex:/^[a-z0-9_\-]+$/',
        ];

        if ($content?->isProtected()) {
            $keyRules[] = Rule::in([$content->key]);
        } else {
            $keyRules[] = Rule::unique('parish_contents', 'key')->ignore($contentId);
        }

        return [
            'key' => $keyRules,
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'highlights' => ['nullable', 'string', 'max:2000'],
            'cta_text' => ['nullable', 'string', 'max:100'],
            'cta_url' => ['nullable', 'string', 'max:255'],
            'use_detail_page' => ['nullable', 'boolean'],
            'display_order' => ['required', 'integer', 'min:0', 'max:999'],
            'is_active' => ['nullable', 'boolean'],
            'images' => ['nullable', 'array', 'max:12'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'remove_image_ids' => ['nullable', 'array'],
            'remove_image_ids.*' => ['integer'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'use_detail_page' => $this->boolean('use_detail_page'),
        ]);
    }

    public function messages(): array
    {
        return [
            'key.in' => 'La clave de esta seccion base no se puede cambiar.',
        ];
    }
}
