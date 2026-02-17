<?php

namespace Tests\Feature;

use App\Models\Content;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_content(): void
    {
        $payload = [
            'name' => 'Evaluación diagnóstica',
            'description' => 'Se registra el estado inicial del proyecto.',
            'roles' => ['professor', 'student'],
        ];

        $response = $this->postJson('/api/contents', $payload);

        $response
            ->assertCreated()
            ->assertJsonFragment([
                'name' => 'Evaluación diagnóstica',
            ])
            ->assertJsonPath('data.roles', ['professor', 'student']);

        $this->assertDatabaseHas('contents', [
            'name' => 'Evaluación diagnóstica',
        ]);
    }

    public function test_rejects_invalid_role(): void
    {
        $response = $this->postJson('/api/contents', [
            'name' => 'Meta inválida',
            'roles' => ['invalid-role'],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['roles.0']);
    }

    public function test_prevents_deleting_content_in_use(): void
    {
        $content = Content::create([
            'name' => 'Indicador de prueba',
            'description' => 'Contenido asociado a una versión.',
            'roles' => ['professor'],
        ]);

        // Crear proyecto y versión asociados
        $project = $this->createProject();
        $version = $project->versions()->create();

        $content->contentVersions()->create([
            'version_id' => $version->id,
            'value' => 'Valor ejemplo',
        ]);

        $response = $this->deleteJson('/api/contents/' . $content->id);

        $response->assertStatus(409);
        $this->assertDatabaseHas('contents', [
            'id' => $content->id,
        ]);
    }

    private function createProject()
    {
        $researchGroup = \App\Models\ResearchGroup::create([
            'name' => 'Grupo de Investigación',
            'initials' => 'GI',
            'description' => 'Descripción del grupo',
        ]);

        $investigationLine = \App\Models\InvestigationLine::create([
            'name' => 'Línea de investigación',
            'description' => 'Descripción de la línea',
            'research_group_id' => $researchGroup->id,
        ]);

        $thematicArea = \App\Models\ThematicArea::create([
            'name' => 'Área temática',
            'description' => 'Descripción área',
            'investigation_line_id' => $investigationLine->id,
        ]);

        $projectStatus = new \App\Models\ProjectStatus();
        $projectStatus->name = 'Activo';
        $projectStatus->description = 'Estado activo';
        $projectStatus->save();

        return \App\Models\Project::create([
            'title' => 'Proyecto ejemplo',
            'evaluation_criteria' => 'Criterios iniciales',
            'thematic_area_id' => $thematicArea->id,
            'project_status_id' => $projectStatus->id,
        ]);
    }
}
