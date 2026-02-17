<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FrameworkRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'        => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'start_year'  => ['nullable','integer','between:1900,2100'],
            'end_year'    => ['nullable','integer','between:1900,2100','gte:start_year'],
        ];
    }
}
