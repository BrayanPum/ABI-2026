<?php

namespace App\Http\Requests;

use App\Models\AcademicProcessWindow;
use App\Models\ResearchStaff\ResearchStaffAcademicPeriod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AcademicProcessWindowRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $processKey = $this->input('process_key');
        $requiresEvaluation = $processKey === AcademicProcessWindow::PROCESS_IDEA_SELECTION
            ? $this->boolean('requires_evaluation')
            : false;

        $this->merge([
            'name' => trim((string) $this->input('name')),
            'notes' => trim((string) $this->input('notes')),
            'requires_evaluation' => $requiresEvaluation,
        ]);
    }

    public function rules(): array
    {
        $windowId = $this->route('academic_process_window')?->id;

        return [
            'academic_period_id' => ['required', 'integer', Rule::exists('academic_periods', 'id')->whereNull('deleted_at')],
            'process_key' => [
                'required',
                Rule::in(array_keys(AcademicProcessWindow::processOptions())),
                Rule::unique('academic_process_windows', 'process_key')
                    ->where(fn ($query) => $query
                        ->where('academic_period_id', $this->input('academic_period_id'))
                        ->whereNull('deleted_at'))
                    ->ignore($windowId),
            ],
            'name' => ['nullable', 'string', 'max:150'],
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after:start_at'],
            'notes' => ['nullable', 'string'],
            'is_enabled' => ['required', 'boolean'],
            'requires_evaluation' => ['required', 'boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $period = ResearchStaffAcademicPeriod::query()->find($this->input('academic_period_id'));

            if (! $period) {
                return;
            }

            $startAt = $this->date('start_at');
            $endAt = $this->date('end_at');

            if ($startAt && $period->start_date && $startAt->lt($period->start_date->copy()->startOfDay())) {
                $validator->errors()->add('start_at', 'La apertura del proceso no puede iniciar antes del periodo academico.');
            }

            if ($endAt && $period->end_date && $endAt->gt($period->end_date->copy()->endOfDay())) {
                $validator->errors()->add('end_at', 'El cierre del proceso no puede superar la fecha final del periodo academico.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'academic_period_id.required' => 'Debes seleccionar un periodo academico.',
            'process_key.required' => 'Debes seleccionar el proceso.',
            'process_key.unique' => 'Ya existe una ventana para este proceso dentro del periodo seleccionado.',
            'start_at.required' => 'La fecha de apertura es obligatoria.',
            'end_at.required' => 'La fecha de cierre es obligatoria.',
            'end_at.after' => 'La fecha de cierre debe ser posterior a la fecha de apertura.',
        ];
    }
}
