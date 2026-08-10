<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class ProjectRequest extends FormRequest
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
        if ($this->has('title')) {
            $this->merge([
                'title' => trim((string) $this->input('title')),
            ]);
        }

        if ($this->has('evaluation_criteria')) {
            $value = $this->input('evaluation_criteria');
            $this->merge([
                'evaluation_criteria' => $value === null ? null : trim((string) $value),
            ]);
        }

        foreach (['professor_ids', 'student_ids'] as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => $this->normalizeIds($this->input($field)),
                ]);
            }
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'evaluation_criteria' => ['nullable', 'string'],
            'thematic_area_id' => [
                'required',
                'integer',
                Rule::exists('thematic_areas', 'id')->whereNull('deleted_at'),
            ],
            'project_status_id' => [
                'required',
                'integer',
                Rule::exists('project_statuses', 'id')->whereNull('deleted_at'),
            ],
            'professor_ids' => ['sometimes', 'array'],
            'professor_ids.*' => [
                'integer',
                'distinct',
                Rule::exists('professors', 'id')->whereNull('deleted_at'),
            ],
            'student_ids' => ['sometimes', 'array'],
            'student_ids.*' => [
                'integer',
                'distinct',
                Rule::exists('students', 'id')->whereNull('deleted_at'),
            ],
        ];
    }

    /**
     * Custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'El título del proyecto es obligatorio.',
            'title.max' => 'El título del proyecto no puede superar los 255 caracteres.',
            'thematic_area_id.required' => 'Debes seleccionar un área temática.',
            'thematic_area_id.exists' => 'El área temática seleccionada no existe o está inactiva.',
            'project_status_id.required' => 'Debes seleccionar un estado del proyecto.',
            'project_status_id.exists' => 'El estado del proyecto seleccionado no existe o está inactivo.',
            'professor_ids.*.exists' => 'El profesor seleccionado no existe o está inactivo.',
            'student_ids.*.exists' => 'El estudiante seleccionado no existe o está inactivo.',
        ];
    }

    /**
     * Retrieve the validated data while preserving array keys.
     */
    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);

        foreach (['professor_ids', 'student_ids'] as $relation) {
            if (array_key_exists($relation, $data)) {
                $data[$relation] = Arr::values($data[$relation]);
            }
        }

        return $data;
    }

    /**
     * Normalize a list of identifiers into an array of unique integers.
     */
    protected function normalizeIds($value): array
    {
        if ($value === null) {
            return [];
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $value = $decoded;
            } else {
                $value = array_filter(array_map('trim', explode(',', $value)), static fn ($id) => $id !== '');
            }
        }

        if (! is_array($value)) {
            $value = [$value];
        }

        $value = array_filter($value, static fn ($id) => $id !== null && $id !== '');

        return array_values(array_unique(array_map('intval', $value)));
    }
}
