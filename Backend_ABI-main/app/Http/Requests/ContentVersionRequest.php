<?php

namespace App\Http\Requests;

use App\Models\ContentVersion;
use Illuminate\Foundation\Http\FormRequest;

class ContentVersionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('value')) {
            $value = $this->input('value');
            $this->merge([
                'value' => $value === null ? null : trim((string) $value),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $contentVersion = $this->route('content_version');
        $contentVersionId = is_object($contentVersion) ? $contentVersion->getKey() : $contentVersion;

        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');
        $requiredRule = $isUpdate ? 'sometimes' : 'required';

        return [
            'content_id' => [
                $requiredRule,
                'integer',
                'exists:contents,id',
            ],
            'version_id' => [
                $requiredRule,
                'integer',
                'exists:versions,id',
            ],
            'value' => [
                $requiredRule,
                'string',
            ],
            'content_version_id' => [
                'prohibited',
            ],
        ];
    }

    /**
     * Configure the validator instance to prevent duplicate combinations.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $contentVersion = $this->route('content_version');
            $contentVersionId = is_object($contentVersion) ? $contentVersion->getKey() : $contentVersion;

            $contentId = $this->input('content_id');
            $versionId = $this->input('version_id');

            if ($contentId === null || $versionId === null) {
                if ($contentVersion instanceof ContentVersion) {
                    $contentId = $contentId ?? $contentVersion->content_id;
                    $versionId = $versionId ?? $contentVersion->version_id;
                } else {
                    return;
                }
            }

            $query = ContentVersion::query()
                ->where('content_id', $contentId)
                ->where('version_id', $versionId);

            if ($contentVersionId) {
                $query->whereKeyNot($contentVersionId);
            }

            if ($query->exists()) {
                $validator->errors()->add('content_id', 'Ya existe un valor registrado para este contenido en la versión seleccionada.');
            }
        });
    }
}
