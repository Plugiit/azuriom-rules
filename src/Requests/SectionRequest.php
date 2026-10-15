<?php

namespace Azuriom\Plugin\Rules\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SectionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'content' => ['required', 'string', 'max:100000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => trans('rules::admin.sections.fields.title'),
            'content' => trans('rules::admin.sections.fields.content'),
        ];
    }
}
