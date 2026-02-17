<?php

namespace App\Http\Requests;

use App\Models\Content;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

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
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('name')) {
            $this->merge([
                'name' => trim((string) $this->input('name')),
            ]);
        }

        if ($this->has('description')) {
            $description = $this->input('description');
            $this->merge([
                'description' => $description === null ? null : trim((string) $description),
            ]);
        }

        if ($this->has('roles')) {
            $roles = $this->input('roles');

            if (is_string($roles)) {
                $decoded = json_decode($roles, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $roles = $decoded;
                } else {
                    $roles = array_filter(array_map('trim', explode(',', $roles)));
                }
            }

            if ($roles === null) {
                $roles = [];
            }

            if (! is_array($roles)) {
                $roles = [$roles];
            }

            $roles = array_values(array_unique(array_filter($roles, static fn ($role) => $role !== null && $role !== '')));

            $this->merge(['roles' => $roles]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $content = $this->route('content');
        $contentId = is_object($content) ? $content->getKey() : $content;

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('contents', 'name')->ignore($contentId),
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'roles' => [
                'required',
                'array',
                'min:1',
            ],
            'roles.*' => [
                'required',
                'string',
                'distinct',
                Rule::in(Content::ALLOWED_ROLES),
            ],
        ];
    }

    /**
     * Custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'roles.*.in' => 'El rol seleccionado no es válido.',
        ];
    }

    /**
     * Retrieve the validated data with normalized roles order.
     */
    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);

        if (isset($data['roles'])) {
            $data['roles'] = Arr::sort($data['roles']);
        }

        return $data;
    }
}
