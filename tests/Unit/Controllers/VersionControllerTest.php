<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\ResearchStaff\ResearchStaffVersion;
use App\Models\ResearchStaff\ResearchStaffProject;
use App\Models\ResearchStaff\ResearchStaffUser;
use Illuminate\Support\Facades\Hash;

class VersionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $project;

    protected function setUp(): void
    {
        parent::setUp();
        // Note: This is a simplified setup. In production you'd need to create all related models
        $this->project = ResearchStaffProject::create([
            'title' => 'Proyecto Test',
            'delivery_date' => now(),
            'project_status_id' => 1,
            'thematic_area_id' => 1
        ]);
    }

    /** @test */
    public function test_can_list_versions_as_json()
    {
        $user = $this->createAuthUser();
        ResearchStaffVersion::create([
            'project_id' => $this->project->id,
            'name' => 'Versión 1.0'
        ]);

        $response = $this->actingAs($user)->getJson(route('versions.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    /** @test */
    public function test_can_filter_versions_by_project()
    {
        $user = $this->createAuthUser();

        $response = $this->actingAs($user)->getJson(route('versions.index', ['project_id' => $this->project->id]));

        $response->assertStatus(200);
    }

    /** @test */
    public function test_can_create_version()
    {
        $user = $this->createAuthUser();
        $data = [
            'project_id' => $this->project->id,
            'name' => 'Nueva Versión'
        ];

        $response = $this->actingAs($user)->postJson(route('versions.store'), $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('versions', ['name' => 'Nueva Versión']);
    }

    /** @test */
    public function test_can_show_version()
    {
        $user = $this->createAuthUser();
        $version = ResearchStaffVersion::create([
            'project_id' => $this->project->id,
            'name' => 'Versión Test'
        ]);

        $response = $this->actingAs($user)->getJson(route('versions.show', $version));

        $response->assertStatus(200);
        $response->assertJsonStructure(['id', 'name', 'project_id']);
    }

    /** @test */
    public function test_can_update_version()
    {
        $user = $this->createAuthUser();
        $version = ResearchStaffVersion::create([
            'project_id' => $this->project->id,
            'name' => 'Versión Original'
        ]);

        $data = [
            'project_id' => $this->project->id,
            'name' => 'Versión Actualizada'
        ];

        $response = $this->actingAs($user)->putJson(route('versions.update', $version), $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('versions', ['name' => 'Versión Actualizada']);
    }

    /** @test */
    public function test_can_soft_delete_version()
    {
        $user = $this->createAuthUser();
        $version = ResearchStaffVersion::create([
            'project_id' => $this->project->id,
            'name' => 'Versión Test'
        ]);

        $response = $this->actingAs($user)->deleteJson(route('versions.destroy', $version));

        $response->assertStatus(204);
        $this->assertSoftDeleted('versions', ['id' => $version->id]);
    }

    /** @test */
    public function test_cannot_update_deleted_version()
    {
        $user = $this->createAuthUser();
        $version = ResearchStaffVersion::create([
            'project_id' => $this->project->id,
            'name' => 'Versión Test'
        ]);
        $version->delete();

        $data = [
            'project_id' => $this->project->id,
            'name' => 'Versión Actualizada'
        ];

        $response = $this->actingAs($user)->putJson(route('versions.update', $version->id), $data);

        $response->assertStatus(404);
    }

    /** @test */
    public function test_can_restore_deleted_version()
    {
        $user = $this->createAuthUser();
        $version = ResearchStaffVersion::create([
            'project_id' => $this->project->id,
            'name' => 'Versión Test'
        ]);
        $version->delete();

        $response = $this->actingAs($user)->postJson(route('versions.restore', $version->id));

        $response->assertStatus(200);
        $this->assertDatabaseHas('versions', ['id' => $version->id, 'deleted_at' => null]);
    }

    /** @test */
    public function test_requires_authentication()
    {
        $response = $this->getJson(route('versions.index'));

        $response->assertStatus(401);
    }

    private function createAuthUser(): ResearchStaffUser
    {
        return ResearchStaffUser::create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role' => 'research_staff',
            'state' => 1,
        ]);
    }
}
