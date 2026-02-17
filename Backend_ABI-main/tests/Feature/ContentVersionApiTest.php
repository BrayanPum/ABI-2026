<?php

namespace Tests\Feature;

use App\Models\Content;
use App\Models\ContentVersion;
use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\ResearchGroup;
use App\Models\InvestigationLine;
use App\Models\ThematicArea;
use App\Models\Version;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentVersionApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_store_content_version(): void
    {
        [$content, $version] = $this->createContentAndVersion();

        $response = $this->postJson('/api/content-versions', [
            'content_id' => $content->id,
            'version_id' => $version->id,
            'value' => 'Valor diligenciado',
        ]);

        $response
            ->assertCreated()
            ->assertJsonFragment([
                'value' => 'Valor diligenciado',
            ]);

        $this->assertDatabaseHas('content_version', [
            'content_id' => $content->id,
            'version_id' => $version->id,
            'value' => 'Valor diligenciado',
        ]);
    }

    public function test_prevents_duplicate_content_version(): void
    {
        [$content, $version] = $this->createContentAndVersion();

        ContentVersion::create([
            'content_id' => $content->id,
            'version_id' => $version->id,
            'value' => 'Valor inicial',
        ]);

        $response = $this->postJson('/api/content-versions', [
            'content_id' => $content->id,
            'version_id' => $version->id,
            'value' => 'Duplicado',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['content_id']);
    }

    private function createContentAndVersion(): array
    {
        $content = Content::create([
            'name' => 'Elemento evaluable',
            'description' => 'Contenido base para la versión.',
            'roles' => ['research_staff'],
        ]);

        $researchGroup = ResearchGroup::create([
            'name' => 'Grupo',
            'initials' => 'GR',
            'description' => 'Grupo de investigación',
        ]);

        $investigationLine = InvestigationLine::create([
            'name' => 'Línea',
            'description' => 'Línea base',
            'research_group_id' => $researchGroup->id,
        ]);

        $thematicArea = ThematicArea::create([
            'name' => 'Área',
            'description' => 'Área base',
            'investigation_line_id' => $investigationLine->id,
        ]);

        $projectStatus = new ProjectStatus();
        $projectStatus->name = 'Activo';
        $projectStatus->description = 'Estado activo';
        $projectStatus->save();

        $project = Project::create([
            'title' => 'Proyecto base',
            'evaluation_criteria' => 'Criterios',
            'thematic_area_id' => $thematicArea->id,
            'project_status_id' => $projectStatus->id,
        ]);

        $version = Version::create([
            'project_id' => $project->id,
        ]);

        return [$content, $version];
    }
}
