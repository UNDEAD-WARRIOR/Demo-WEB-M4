<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $mediaId = $this->route('media')?->id;

        return [
            'title' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('media', 'title')->ignore($mediaId),
            ],
            'release_year' => 'sometimes|nullable|integer|min:1900|max:2100',
            'genres' => 'sometimes|nullable|array',
            'genres.*' => 'string',
            'type' => 'sometimes|nullable|string|max:255',
            'episodes' => 'sometimes|nullable|integer|min:1',
            'score' => 'sometimes|nullable|numeric|min:0|max:10',
        ];
    }
}
