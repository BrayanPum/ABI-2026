<?php

namespace Database\Seeders;

use App\Models\FormatoCampo;
use App\Models\FormatoTipo;
use Illuminate\Database\Seeder;

class FormatoSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Acta de Reunión ─────────────────────────────────────────────
        $acta = FormatoTipo::create([
            'nombre'       => 'Acta de Reunión',
            'codigo'       => 'FOR-INV-004',
            'descripcion'  => 'Documenta las reuniones de seguimiento, acuerdos y compromisos establecidos entre los participantes del proyecto.',
            'icono'        => 'ti ti-writing',
            'color'        => 'blue',
            'roles_acceso' => ['research_staff', 'professor', 'committee_leader', 'student'],
            'activo'       => true,
        ]);

        $this->campos($acta, [
            ['etiqueta' => 'Tema / Agenda propuesta',         'tipo' => 'textarea', 'requerido' => true,  'seccion' => '1. Información General'],
            ['etiqueta' => 'Nombres y Apellidos investigador', 'tipo' => 'texto',    'requerido' => true,  'seccion' => null],
            ['etiqueta' => 'Grupo de Investigación',          'tipo' => 'texto',    'requerido' => true,  'seccion' => null],
            ['etiqueta' => 'Programa Académico',              'tipo' => 'texto',    'requerido' => true,  'seccion' => null],
            ['etiqueta' => 'Fecha de realización',            'tipo' => 'fecha',    'requerido' => true,  'seccion' => null],
            ['etiqueta' => 'Medio y/o ubicación',             'tipo' => 'texto',    'requerido' => true,  'seccion' => null],
            ['etiqueta' => 'Hora inicial',                    'tipo' => 'hora',     'requerido' => false, 'seccion' => null],
            ['etiqueta' => 'Hora finaliza',                   'tipo' => 'hora',     'requerido' => false, 'seccion' => null],
            ['etiqueta' => 'Orden del día',                   'tipo' => 'textarea', 'requerido' => false, 'seccion' => null],

            ['etiqueta' => 'Listado de asistentes',           'tipo' => 'textarea', 'requerido' => false, 'seccion' => 'Asistentes'],
            ['etiqueta' => 'Docentes asistentes',             'tipo' => 'textarea', 'requerido' => false, 'seccion' => null],
            ['etiqueta' => 'Estudiantes asistentes',          'tipo' => 'textarea', 'requerido' => false, 'seccion' => null],

            ['etiqueta' => 'Desarrollo de la reunión',        'tipo' => 'textarea', 'requerido' => false, 'seccion' => 'Desarrollo de la Reunión'],

            ['etiqueta' => 'Plan de acción',                  'tipo' => 'textarea', 'requerido' => false, 'seccion' => 'Plan de Acción'],
            ['etiqueta' => 'Responsable',                     'tipo' => 'texto',    'requerido' => false, 'seccion' => null],
            ['etiqueta' => 'Fecha límite',                    'tipo' => 'fecha',    'requerido' => false, 'seccion' => null],
            ['etiqueta' => 'Evidencia',                       'tipo' => 'textarea', 'requerido' => false, 'seccion' => null],

            ['etiqueta' => 'Eficacia de la reunión (%)',      'tipo' => 'numero',   'requerido' => false, 'seccion' => 'Eficacia y Próxima Reunión'],
            ['etiqueta' => 'Próxima reunión — Fecha',         'tipo' => 'fecha',    'requerido' => false, 'seccion' => null],
            ['etiqueta' => 'Próxima reunión — Lugar',         'tipo' => 'texto',    'requerido' => false, 'seccion' => null],
            ['etiqueta' => 'Próxima reunión — Hora',          'tipo' => 'hora',     'requerido' => false, 'seccion' => null],

            ['etiqueta' => 'Preparado por',                   'tipo' => 'texto',    'requerido' => false, 'seccion' => 'Firmas'],
            ['etiqueta' => 'Aprobado por',                    'tipo' => 'texto',    'requerido' => false, 'seccion' => null],
            ['etiqueta' => 'Revisado por',                    'tipo' => 'texto',    'requerido' => false, 'seccion' => null],
        ]);

        // ─── Ideas de Estudiante ─────────────────────────────────────────
        $ideas = FormatoTipo::create([
            'nombre'       => 'Ideas de Estudiante',
            'codigo'       => 'FOR-INV-005',
            'descripcion'  => 'Permite a los estudiantes registrar y presentar sus ideas de proyecto ante el comité evaluador.',
            'icono'        => 'ti ti-bulb',
            'color'        => 'green',
            'roles_acceso' => ['research_staff', 'student'],
            'activo'       => true,
        ]);

        $this->campos($ideas, [
            ['etiqueta' => 'Título de la Idea', 'tipo' => 'texto',    'requerido' => true,  'seccion' => 'Información Básica'],
            ['etiqueta' => 'Docente',            'tipo' => 'texto',    'requerido' => false, 'seccion' => null],

            ['etiqueta' => 'Viabilidad',                  'tipo' => 'checkbox', 'requerido' => false, 'seccion' => 'Criterios de Evaluación'],
            ['etiqueta' => 'Pertinencia con el Programa', 'tipo' => 'checkbox', 'requerido' => false, 'seccion' => null],
            ['etiqueta' => 'Disponibilidad de Docentes',  'tipo' => 'checkbox', 'requerido' => false, 'seccion' => null],
            ['etiqueta' => 'Calidad Título vs Objetivos', 'tipo' => 'checkbox', 'requerido' => false, 'seccion' => null],

            ['etiqueta' => 'Concepto',  'tipo' => 'select', 'requerido' => false, 'seccion' => 'Concepto',
             'opciones' => [['valor' => 'aprobada', 'etiqueta' => 'Aprobada'], ['valor' => 'no_aprobada', 'etiqueta' => 'No Aprobada']]],
            ['etiqueta' => 'N° Acta',   'tipo' => 'texto',  'requerido' => false, 'seccion' => null],
            ['etiqueta' => 'VoBo. Dirección de Investigaciones', 'tipo' => 'texto', 'requerido' => false, 'seccion' => null],

            ['etiqueta' => 'Observaciones', 'tipo' => 'textarea', 'requerido' => false, 'seccion' => 'Observaciones'],
        ]);

        // ─── Ficha de Propuesta ──────────────────────────────────────────
        $ficha = FormatoTipo::create([
            'nombre'       => 'Ficha de Propuesta',
            'codigo'       => 'FOR-INV-006',
            'descripcion'  => 'Permite a los profesores registrar propuestas de temas de grado en el banco de proyectos institucional.',
            'icono'        => 'ti ti-file-description',
            'color'        => 'orange',
            'roles_acceso' => ['research_staff', 'professor', 'committee_leader'],
            'activo'       => true,
        ]);

        $this->campos($ficha, [
            ['etiqueta' => 'Ciudad',                       'tipo' => 'texto',    'requerido' => true,  'seccion' => '1. Información General'],
            ['etiqueta' => 'Fecha de Propuesta',           'tipo' => 'fecha',    'requerido' => true,  'seccion' => null],
            ['etiqueta' => 'Cantidad de Estudiantes',      'tipo' => 'numero',   'requerido' => true,  'seccion' => null],
            ['etiqueta' => 'Tiempo de Ejecución (meses)',  'tipo' => 'numero',   'requerido' => true,  'seccion' => null],

            ['etiqueta' => 'Título del Proyecto', 'tipo' => 'texto', 'requerido' => true, 'seccion' => '2. Datos del Tema'],
            ['etiqueta' => 'Tipo de Investigación', 'tipo' => 'select', 'requerido' => true, 'seccion' => null,
             'opciones' => [['valor' => 'documental', 'etiqueta' => 'Documental'], ['valor' => 'experimental', 'etiqueta' => 'Experimental'], ['valor' => 'campo', 'etiqueta' => 'De Campo']]],
            ['etiqueta' => 'Línea de Investigación', 'tipo' => 'texto', 'requerido' => true,  'seccion' => null],
            ['etiqueta' => 'Área Temática',          'tipo' => 'texto', 'requerido' => true,  'seccion' => null],

            ['etiqueta' => 'Objetivo General',       'tipo' => 'textarea', 'requerido' => true,  'seccion' => '3. Objetivos'],
            ['etiqueta' => 'Objetivo Específico 1',  'tipo' => 'textarea', 'requerido' => true,  'seccion' => null],
            ['etiqueta' => 'Objetivo Específico 2',  'tipo' => 'textarea', 'requerido' => false, 'seccion' => null],
            ['etiqueta' => 'Objetivo Específico 3',  'tipo' => 'textarea', 'requerido' => false, 'seccion' => null],

            ['etiqueta' => 'Pertinencia con el Grupo de Investigación y Programa', 'tipo' => 'textarea', 'requerido' => true, 'seccion' => '4. Pertinencia, Viabilidad y Recursos'],
            ['etiqueta' => 'Disponibilidad de Docentes',                           'tipo' => 'textarea', 'requerido' => true, 'seccion' => null],
            ['etiqueta' => 'Calidad y Correspondencia Título-Objetivos',           'tipo' => 'textarea', 'requerido' => true, 'seccion' => null],
            ['etiqueta' => 'Recursos Requeridos',                                  'tipo' => 'textarea', 'requerido' => true, 'seccion' => null],

            ['etiqueta' => 'Descripción del Tema',                             'tipo' => 'textarea', 'requerido' => true,  'seccion' => '5. Descripción y Contexto'],
            ['etiqueta' => 'ODS (Objetivos de Desarrollo Sostenible)',         'tipo' => 'textarea', 'requerido' => false, 'seccion' => null],
            ['etiqueta' => 'Plan de Desarrollo Nacional/Departamental/Municipal', 'tipo' => 'textarea', 'requerido' => false, 'seccion' => null],

            ['etiqueta' => 'Estado', 'tipo' => 'select', 'requerido' => false, 'seccion' => 'Estado',
             'opciones' => [['valor' => 'pendiente', 'etiqueta' => 'Pendiente'], ['valor' => 'aprobada', 'etiqueta' => 'Aprobada'], ['valor' => 'rechazada', 'etiqueta' => 'Rechazada']]],
        ]);
    }

    private function campos(FormatoTipo $tipo, array $campos): void
    {
        foreach ($campos as $orden => $data) {
            FormatoCampo::create([
                'formato_tipo_id' => $tipo->id,
                'nombre'          => \Illuminate\Support\Str::slug($data['etiqueta'], '_'),
                'etiqueta'        => $data['etiqueta'],
                'tipo'            => $data['tipo'],
                'opciones'        => $data['opciones'] ?? null,
                'requerido'       => $data['requerido'],
                'seccion'         => $data['seccion'],
                'orden'           => $orden,
            ]);
        }
    }
}
