<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\ResearchStaff\ResearchStaffResearchGroup;
use App\Models\ResearchStaff\ResearchStaffUser;
use Illuminate\Support\Facades\Hash;

class ResearchGroupControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    // ========================
    // CREATE TESTS
    // ========================

    /** @test */
    public function test_can_create_research_group()
    {
        $user = $this->createAuthUser();
        $data = [
            'name' => 'Grupo de Investigación Test',
            'initials' => 'GIT',
            'description' => 'Esta es una descripción de prueba muy larga'
        ];

        $response = $this->actingAs($user)->post(route('research-groups.store'), $data);

        $response->assertRedirect(route('research-groups.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('research_groups', ['name' => 'Grupo de Investigación Test']);
    }

    /** @test */
    public function test_validation_fails_with_short_description()
    {
        $user = $this->createAuthUser();
        $data = [
            'name' => 'Grupo Test',
            'initials' => 'GT',
            'description' => 'Corta' // Menos de 10 caracteres
        ];

        $response = $this->actingAs($user)->post(route('research-groups.store'), $data);

        $response->assertSessionHasErrors('description');
    }

    /** @test */
    public function test_validation_fails_with_duplicate_name()
    {
        $user = $this->createAuthUser();
        ResearchStaffResearchGroup::create([
            'name' => 'Grupo Existente',
            'initials' => 'GE',
            'description' => 'Descripción existente válida'
        ]);

        $data = [
            'name' => 'Grupo Existente',
            'initials' => 'GE2',
            'description' => 'Nueva descripción válida'
        ];

        $response = $this->actingAs($user)->post(route('research-groups.store'), $data);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function test_validation_fails_with_duplicate_initials()
    {
        $user = $this->createAuthUser();
        ResearchStaffResearchGroup::create([
            'name' => 'Grupo Existente',
            'initials' => 'GE',
            'description' => 'Descripción existente válida'
        ]);

        $data = [
            'name' => 'Grupo Nuevo',
            'initials' => 'GE',
            'description' => 'Nueva descripción válida'
        ];

        $response = $this->actingAs($user)->post(route('research-groups.store'), $data);

        $response->assertSessionHasErrors('initials');
    }

    /** @test */
    public function test_validation_fails_with_missing_required_fields()
    {
        $user = $this->createAuthUser();
        $data = [];

        $response = $this->actingAs($user)->post(route('research-groups.store'), $data);

        $response->assertSessionHasErrors(['name', 'initials', 'description']);
    }

    // ========================
    // READ TESTS
    // ========================

    /** @test */
    public function test_can_list_research_groups()
    {
        $user = $this->createAuthUser();
        ResearchStaffResearchGroup::create([
            'name' => 'Grupo 1',
            'initials' => 'G1',
            'description' => 'Descripción del grupo 1'
        ]);

        $response = $this->actingAs($user)->get(route('research-groups.index'));

        $response->assertStatus(200);
        $response->assertViewIs('research-groups.index');
        $response->assertViewHas('researchGroups');
    }

    /** @test */
    public function test_can_show_research_group()
    {
        $user = $this->createAuthUser();
        $group = ResearchStaffResearchGroup::create([
            'name' => 'Grupo Test',
            'initials' => 'GT',
            'description' => 'Descripción del grupo test'
        ]);

        $response = $this->actingAs($user)->get(route('research-groups.show', $group));

        $response->assertStatus(200);
        $response->assertViewIs('research-groups.show');
        $response->assertViewHas('researchGroup');
    }

    /** @test */
    public function test_can_search_research_groups()
    {
        $user = $this->createAuthUser();
        ResearchStaffResearchGroup::create([
            'name' => 'Grupo Especial',
            'initials' => 'GE',
            'description' => 'Descripción especial'
        ]);

        $response = $this->actingAs($user)->get(route('research-groups.index', ['search' => 'Especial']));

        $response->assertStatus(200);
        $response->assertViewHas('search', 'Especial');
    }

    /** @test */
    public function test_pagination_works_correctly()
    {
        $user = $this->createAuthUser();

        $response = $this->actingAs($user)->get(route('research-groups.index', ['per_page' => 10]));

        $response->assertStatus(200);
        $response->assertViewHas('perPage', 10);
    }

    /** @test */
    public function test_returns_404_for_nonexistent_resource()
    {
        $user = $this->createAuthUser();

        $response = $this->actingAs($user)->get(route('research-groups.show', 99999));

        $response->assertStatus(404);
    }

    // ========================
    // UPDATE TESTS
    // ========================

    /** @test */
    public function test_can_update_research_group()
    {
        $user = $this->createAuthUser();
        $group = ResearchStaffResearchGroup::create([
            'name' => 'Grupo Original',
            'initials' => 'GO',
            'description' => 'Descripción original válida'
        ]);

        $data = [
            'name' => 'Grupo Actualizado',
            'initials' => 'GA',
            'description' => 'Descripción actualizada válida'
        ];

        $response = $this->actingAs($user)->put(route('research-groups.update', $group), $data);

        $response->assertRedirect(route('research-groups.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('research_groups', ['name' => 'Grupo Actualizado']);
    }

    /** @test */
    public function test_cannot_update_deleted_resource()
    {
        $user = $this->createAuthUser();
        $group = ResearchStaffResearchGroup::create([
            'name' => 'Grupo Test',
            'initials' => 'GT',
            'description' => 'Descripción test válida'
        ]);
        $group->delete();

        $data = [
            'name' => 'Grupo Actualizado',
            'initials' => 'GA',
            'description' => 'Descripción actualizada válida'
        ];

        $response = $this->actingAs($user)->put(route('research-groups.update', $group->id), $data);

        $response->assertStatus(404);
    }

    // ========================
    // SOFT DELETE TESTS
    // ========================

    /** @test */
    public function test_can_soft_delete_research_group()
    {
        $user = $this->createAuthUser();
        $group = ResearchStaffResearchGroup::create([
            'name' => 'Grupo a Eliminar',
            'initials' => 'GAE',
            'description' => 'Descripción del grupo a eliminar'
        ]);

        $response = $this->actingAs($user)->delete(route('research-groups.destroy', $group));

        $response->assertRedirect(route('research-groups.index'));
        $response->assertSessionHas('success');
        $this->assertSoftDeleted('research_groups', ['id' => $group->id]);
    }

    /** @test */
    public function test_cannot_delete_already_deleted_resource()
    {
        $user = $this->createAuthUser();
        $group = ResearchStaffResearchGroup::create([
            'name' => 'Grupo Test',
            'initials' => 'GT',
            'description' => 'Descripción test válida'
        ]);
        $group->delete();

        $response = $this->actingAs($user)->delete(route('research-groups.destroy', $group->id));

        $response->assertStatus(404);
    }

    // ========================
    // RESTORE TESTS
    // ========================

    /** @test */
    public function test_can_restore_deleted_resource()
    {
        $user = $this->createAuthUser();
        $group = ResearchStaffResearchGroup::create([
            'name' => 'Grupo a Restaurar',
            'initials' => 'GAR',
            'description' => 'Descripción del grupo a restaurar'
        ]);
        $group->delete();

        $response = $this->actingAs($user)->post(route('research-groups.restore', $group->id));

        $response->assertRedirect(route('research-groups.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('research_groups', [
            'id' => $group->id,
            'deleted_at' => null
        ]);
    }

    /** @test */
    public function test_cannot_restore_non_deleted_resource()
    {
        $user = $this->createAuthUser();
        $group = ResearchStaffResearchGroup::create([
            'name' => 'Grupo Activo',
            'initials' => 'GA',
            'description' => 'Descripción del grupo activo'
        ]);

        $response = $this->actingAs($user)->post(route('research-groups.restore', $group->id));

        $response->assertRedirect(route('research-groups.index'));
        $response->assertSessionHas('error');
    }

    // ========================
    // AUTHORIZATION TESTS
    // ========================

    /** @test */
    public function test_requires_authentication()
    {
        $response = $this->get(route('research-groups.index'));

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
