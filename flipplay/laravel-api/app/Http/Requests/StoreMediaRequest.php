<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255|unique:media,title',
            'release_year' => 'nullable|integer|min:1900|max:2100',
            'genres' => 'nullable|array',
            'genres.*' => 'string',
            'type' => 'nullable|string|max:255',
            'episodes' => 'nullable|integer|min:1',
            'score' => 'nullable|numeric|min:0|max:10',
        ];
    }
}
