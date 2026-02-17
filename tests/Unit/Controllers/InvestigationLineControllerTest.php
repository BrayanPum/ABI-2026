<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\ResearchStaff\ResearchStaffInvestigationLine;
use App\Models\ResearchStaff\ResearchStaffResearchGroup;
use App\Models\ResearchStaff\ResearchStaffUser;
use Illuminate\Support\Facades\Hash;

class InvestigationLineControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $researchGroup;

    protected function setUp(): void
    {
        parent::setUp();
        $this->researchGroup = ResearchStaffResearchGroup::create([
            'name' => 'Grupo Test',
            'initials' => 'GT',
            'description' => 'Descripción del grupo test'
        ]);
    }

    // ========================
    // INDEX TESTS
    // ========================

    /** @test */
    public function test_can_list_investigation_lines()
    {
        $user = $this->createAuthUser();
        ResearchStaffInvestigationLine::create([
            'name' => 'Línea Test',
            'description' => 'Descripción de la línea test',
            'research_group_id' => $this->researchGroup->id
        ]);

        $response = $this->actingAs($user)->get(route('investigation-lines.index'));

        $response->assertStatus(200);
        $response->assertViewIs('investigation-lines.index');
        $response->assertViewHas('investigationLines');
    }

    /** @test */
    public function test_can_search_investigation_lines()
    {
        $user = $this->createAuthUser();
        ResearchStaffInvestigationLine::create([
            'name' => 'Línea Especial',
            'description' => 'Descripción especial',
            'research_group_id' => $this->researchGroup->id
        ]);

        $response = $this->actingAs($user)->get(route('investigation-lines.index', ['search' => 'Especial']));

        $response->assertStatus(200);
        $response->assertViewHas('search', 'Especial');
    }

    /** @test */
    public function test_can_filter_by_research_group()
    {
        $user = $this->createAuthUser();

        $response = $this->actingAs($user)->get(route('investigation-lines.index', [
            'research_group_id' => $this->researchGroup->id
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('researchGroupId', $this->researchGroup->id);
    }

    /** @test */
    public function test_pagination_works_correctly()
    {
        $user = $this->createAuthUser();

        $response = $this->actingAs($user)->get(route('investigation-lines.index', ['per_page' => 10]));

        $response->assertStatus(200);
        $response->assertViewHas('perPage', 10);
    }

    // ========================
    // CREATE TESTS
    // ========================

    /** @test */
    public function test_can_create_investigation_line()
    {
        $user = $this->createAuthUser();
        $data = [
            'name' => 'Nueva Línea',
            'description' => 'Descripción de la nueva línea',
            'research_group_id' => $this->researchGroup->id
        ];

        $response = $this->actingAs($user)->post(route('investigation-lines.store'), $data);

        $response->assertRedirect(route('investigation-lines.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('investigation_lines', ['name' => 'Nueva Línea']);
    }

    /** @test */
    public function test_validation_fails_with_missing_required_fields()
    {
        $user = $this->createAuthUser();
        $data = [];

        $response = $this->actingAs($user)->post(route('investigation-lines.store'), $data);

        $response->assertSessionHasErrors(['name', 'description', 'research_group_id']);
    }

    /** @test */
    public function test_validation_fails_with_short_description()
    {
        $user = $this->createAuthUser();
        $data = [
            'name' => 'Línea Test',
            'description' => 'Corta',
            'research_group_id' => $this->researchGroup->id
        ];

        $response = $this->actingAs($user)->post(route('investigation-lines.store'), $data);

        $response->assertSessionHasErrors('description');
    }

    /** @test */
    public function test_validation_fails_with_duplicate_name()
    {
        $user = $this->createAuthUser();
        ResearchStaffInvestigationLine::create([
            'name' => 'Línea Existente',
            'description' => 'Descripción existente',
            'research_group_id' => $this->researchGroup->id
        ]);

        $data = [
            'name' => 'Línea Existente',
            'description' => 'Nueva descripción',
            'research_group_id' => $this->researchGroup->id
        ];

        $response = $this->actingAs($user)->post(route('investigation-lines.store'), $data);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function test_validation_fails_with_invalid_research_group()
    {
        $user = $this->createAuthUser();
        $data = [
            'name' => 'Nueva Línea',
            'description' => 'Descripción válida',
            'research_group_id' => 99999
        ];

        $response = $this->actingAs($user)->post(route('investigation-lines.store'), $data);

        $response->assertSessionHasErrors('research_group_id');
    }

    // ========================
    // SHOW TESTS
    // ========================

    /** @test */
    public function test_can_show_investigation_line()
    {
        $user = $this->createAuthUser();
        $line = ResearchStaffInvestigationLine::create([
            'name' => 'Línea Test',
            'description' => 'Descripción test',
            'research_group_id' => $this->researchGroup->id
        ]);

        $response = $this->actingAs($user)->get(route('investigation-lines.show', $line));

        $response->assertStatus(200);
        $response->assertViewIs('investigation-lines.show');
        $response->assertViewHas('investigationLine');
    }

    /** @test */
    public function test_returns_404_for_nonexistent_investigation_line()
    {
        $user = $this->createAuthUser();

        $response = $this->actingAs($user)->get(route('investigation-lines.show', 99999));

        $response->assertStatus(404);
    }

    /** @test */
    public function test_cannot_show_deleted_investigation_line()
    {
        $user = $this->createAuthUser();
        $line = ResearchStaffInvestigationLine::create([
            'name' => 'Línea Test',
            'description' => 'Descripción test',
            'research_group_id' => $this->researchGroup->id
        ]);
        $line->delete();

        $response = $this->actingAs($user)->get(route('investigation-lines.show', $line->id));

        $response->assertStatus(404);
    }

    // ========================
    // UPDATE TESTS
    // ========================

    /** @test */
    public function test_can_update_investigation_line()
    {
        $user = $this->createAuthUser();
        $line = ResearchStaffInvestigationLine::create([
            'name' => 'Línea Original',
            'description' => 'Descripción original',
            'research_group_id' => $this->researchGroup->id
        ]);

        $data = [
            'name' => 'Línea Actualizada',
            'description' => 'Descripción actualizada',
            'research_group_id' => $this->researchGroup->id
        ];

        $response = $this->actingAs($user)->put(route('investigation-lines.update', $line), $data);

        $response->assertRedirect(route('investigation-lines.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('investigation_lines', ['name' => 'Línea Actualizada']);
    }

    /** @test */
    public function test_cannot_update_deleted_investigation_line()
    {
        $user = $this->createAuthUser();
        $line = ResearchStaffInvestigationLine::create([
            'name' => 'Línea Test',
            'description' => 'Descripción test',
            'research_group_id' => $this->researchGroup->id
        ]);
        $line->delete();

        $data = [
            'name' => 'Línea Actualizada',
            'description' => 'Descripción actualizada',
            'research_group_id' => $this->researchGroup->id
        ];

        $response = $this->actingAs($user)->put(route('investigation-lines.update', $line->id), $data);

        $response->assertStatus(404);
    }

    // ========================
    // DELETE TESTS
    // ========================

    /** @test */
    public function test_can_soft_delete_investigation_line()
    {
        $user = $this->createAuthUser();
        $line = ResearchStaffInvestigationLine::create([
            'name' => 'Línea a Eliminar',
            'description' => 'Descripción a eliminar',
            'research_group_id' => $this->researchGroup->id
        ]);

        $response = $this->actingAs($user)->delete(route('investigation-lines.destroy', $line));

        $response->assertRedirect(route('investigation-lines.index'));
        $response->assertSessionHas('success');
        $this->assertSoftDeleted('investigation_lines', ['id' => $line->id]);
    }

    /** @test */
    public function test_cannot_delete_already_deleted_investigation_line()
    {
        $user = $this->createAuthUser();
        $line = ResearchStaffInvestigationLine::create([
            'name' => 'Línea Test',
            'description' => 'Descripción test',
            'research_group_id' => $this->researchGroup->id
        ]);
        $line->delete();

        $response = $this->actingAs($user)->delete(route('investigation-lines.destroy', $line->id));

        $response->assertStatus(404);
    }

    // ========================
    // RESTORE TESTS
    // ========================

    /** @test */
    public function test_can_restore_deleted_investigation_line()
    {
        $user = $this->createAuthUser();
        $line = ResearchStaffInvestigationLine::create([
            'name' => 'Línea a Restaurar',
            'description' => 'Descripción a restaurar',
            'research_group_id' => $this->researchGroup->id
        ]);
        $line->delete();

        $response = $this->actingAs($user)->post(route('investigation-lines.restore', $line->id));

        $response->assertRedirect(route('investigation-lines.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('investigation_lines', [
            'id' => $line->id,
            'deleted_at' => null
        ]);
    }

    /** @test */
    public function test_cannot_restore_non_deleted_investigation_line()
    {
        $user = $this->createAuthUser();
        $line = ResearchStaffInvestigationLine::create([
            'name' => 'Línea Activa',
            'description' => 'Descripción activa',
            'research_group_id' => $this->researchGroup->id
        ]);

        $response = $this->actingAs($user)->post(route('investigation-lines.restore', $line->id));

        $response->assertRedirect(route('investigation-lines.index'));
        $response->assertSessionHas('error');
    }

    // ========================
    // AUTHORIZATION TESTS
    // ========================

    /** @test */
    public function test_requires_authentication()
    {
        $response = $this->get(route('investigation-lines.index'));

        $response->assertRedirect(route('login'));
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
