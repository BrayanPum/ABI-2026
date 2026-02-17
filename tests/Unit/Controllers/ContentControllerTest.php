<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\ResearchStaff\ResearchStaffContent;
use App\Models\ResearchStaff\ResearchStaffUser;
use Illuminate\Support\Facades\Hash;

class ContentControllerTest extends TestCase
{
    use RefreshDatabase;

    // ========================
    // INDEX TESTS (API)
    // ========================

    /** @test */
    public function test_can_list_contents_as_json()
    {
        $user = $this->createAuthUser();
        ResearchStaffContent::create([
            'name' => 'Contenido Test',
            'description' => 'Descripción del contenido',
            'roles' => ['student', 'professor']
        ]);

        $response = $this->actingAs($user)->getJson(route('contents.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    /** @test */
    public function test_can_search_contents()
    {
        $user = $this->createAuthUser();
        ResearchStaffContent::create([
            'name' => 'Contenido Especial',
            'description' => 'Descripción especial',
            'roles' => ['student']
        ]);

        $response = $this->actingAs($user)->getJson(route('contents.index', ['search' => 'Especial']));

        $response->assertStatus(200);
    }

    /** @test */
    public function test_can_filter_by_roles()
    {
        $user = $this->createAuthUser();
        ResearchStaffContent::create([
            'name' => 'Contenido Profesor',
            'description' => 'Para profesores',
            'roles' => ['professor']
        ]);

        $response = $this->actingAs($user)->getJson(route('contents.index', ['roles' => 'professor']));

        $response->assertStatus(200);
    }

    /** @test */
    public function test_pagination_works_correctly()
    {
        $user = $this->createAuthUser();

        $response = $this->actingAs($user)->getJson(route('contents.index', ['per_page' => 15]));

        $response->assertStatus(200);
    }

    // ========================
    // CREATE TESTS
    // ========================

    /** @test */
    public function test_can_create_content()
    {
        $user = $this->createAuthUser();
        $data = [
            'name' => 'Nuevo Contenido',
            'description' => 'Descripción del nuevo contenido',
            'roles' => ['student', 'professor']
        ];

        $response = $this->actingAs($user)->postJson(route('contents.store'), $data);

        $response->assertStatus(201);
        $response->assertJsonStructure(['message', 'data']);
        $this->assertDatabaseHas('contents', ['name' => 'Nuevo Contenido']);
    }

    /** @test */
    public function test_validation_fails_with_missing_required_fields()
    {
        $user = $this->createAuthUser();
        $data = [];

        $response = $this->actingAs($user)->postJson(route('contents.store'), $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'description', 'roles']);
    }

    // ========================
    // SHOW TESTS
    // ========================

    /** @test */
    public function test_can_show_content()
    {
        $user = $this->createAuthUser();
        $content = ResearchStaffContent::create([
            'name' => 'Contenido Test',
            'description' => 'Descripción test',
            'roles' => ['student']
        ]);

        $response = $this->actingAs($user)->getJson(route('contents.show', $content));

        $response->assertStatus(200);
        $response->assertJsonStructure(['id', 'name', 'description', 'roles']);
    }

    /** @test */
    public function test_returns_404_for_nonexistent_content()
    {
        $user = $this->createAuthUser();

        $response = $this->actingAs($user)->getJson(route('contents.show', 99999));

        $response->assertStatus(404);
    }

    /** @test */
    public function test_cannot_show_deleted_content()
    {
        $user = $this->createAuthUser();
        $content = ResearchStaffContent::create([
            'name' => 'Contenido Test',
            'description' => 'Descripción test',
            'roles' => ['student']
        ]);
        $content->delete();

        $response = $this->actingAs($user)->getJson(route('contents.show', $content->id));

        $response->assertStatus(404);
    }

    // ========================
    // UPDATE TESTS
    // ========================

    /** @test */
    public function test_can_update_content()
    {
        $user = $this->createAuthUser();
        $content = ResearchStaffContent::create([
            'name' => 'Contenido Original',
            'description' => 'Descripción original',
            'roles' => ['student']
        ]);

        $data = [
            'name' => 'Contenido Actualizado',
            'description' => 'Descripción actualizada',
            'roles' => ['professor', 'student']
        ];

        $response = $this->actingAs($user)->putJson(route('contents.update', $content), $data);

        $response->assertStatus(200);
        $response->assertJsonStructure(['message', 'data']);
        $this->assertDatabaseHas('contents', ['name' => 'Contenido Actualizado']);
    }

    /** @test */
    public function test_cannot_update_deleted_content()
    {
        $user = $this->createAuthUser();
        $content = ResearchStaffContent::create([
            'name' => 'Contenido Test',
            'description' => 'Descripción test',
            'roles' => ['student']
        ]);
        $content->delete();

        $data = [
            'name' => 'Contenido Actualizado',
            'description' => 'Descripción actualizada',
            'roles' => ['student']
        ];

        $response = $this->actingAs($user)->putJson(route('contents.update', $content->id), $data);

        $response->assertStatus(404);
    }

    // ========================
    // DELETE TESTS
    // ========================

    /** @test */
    public function test_can_soft_delete_content()
    {
        $user = $this->createAuthUser();
        $content = ResearchStaffContent::create([
            'name' => 'Contenido a Eliminar',
            'description' => 'Será eliminado',
            'roles' => ['student']
        ]);

        $response = $this->actingAs($user)->deleteJson(route('contents.destroy', $content));

        $response->assertStatus(200);
        $this->assertSoftDeleted('contents', ['id' => $content->id]);
    }

    /** @test */
    public function test_cannot_delete_already_deleted_content()
    {
        $user = $this->createAuthUser();
        $content = ResearchStaffContent::create([
            'name' => 'Contenido Test',
            'description' => 'Descripción test',
            'roles' => ['student']
        ]);
        $content->delete();

        $response = $this->actingAs($user)->deleteJson(route('contents.destroy', $content->id));

        $response->assertStatus(404);
    }

    // ========================
    // RESTORE TESTS
    // ========================

    /** @test */
    public function test_can_restore_deleted_content()
    {
        $user = $this->createAuthUser();
        $content = ResearchStaffContent::create([
            'name' => 'Contenido a Restaurar',
            'description' => 'Será restaurado',
            'roles' => ['student']
        ]);
        $content->delete();

        $response = $this->actingAs($user)->postJson(route('contents.restore', $content->id));

        $response->assertStatus(200);
        $this->assertDatabaseHas('contents', [
            'id' => $content->id,
            'deleted_at' => null
        ]);
    }

    /** @test */
    public function test_cannot_restore_non_deleted_content()
    {
        $user = $this->createAuthUser();
        $content = ResearchStaffContent::create([
            'name' => 'Contenido Activo',
            'description' => 'Está activo',
            'roles' => ['student']
        ]);

        $response = $this->actingAs($user)->postJson(route('contents.restore', $content->id));

        $response->assertStatus(400);
    }

    // ========================
    // AUTHORIZATION TESTS
    // ========================

    /** @test */
    public function test_requires_authentication()
    {
        $response = $this->getJson(route('contents.index'));

        $response->assertStatus(401);
    }

    // ========================
    // HELPER METHODS
    // ========================

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
