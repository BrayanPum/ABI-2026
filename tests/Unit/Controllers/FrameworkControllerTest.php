<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\ResearchStaff\ResearchStaffFramework;
use App\Models\ResearchStaff\ResearchStaffUser;
use Illuminate\Support\Facades\Hash;

class FrameworkControllerTest extends TestCase
{
    use RefreshDatabase;

    // ========================
    // INDEX TESTS
    // ========================

    /** @test */
    public function test_can_list_frameworks()
    {
        $user = $this->createAuthUser();
        ResearchStaffFramework::create([
            'name' => 'Framework Test',
            'description' => 'Descripción del framework test',
            'link' => 'https://example.com',
            'start_year' => 2020,
            'end_year' => 2025
        ]);

        $response = $this->actingAs($user)->get(route('frameworks.index'));

        $response->assertStatus(200);
        $response->assertViewIs('framework.index');
        $response->assertViewHas('frameworks');
    }

    /** @test */
    public function test_can_search_frameworks()
    {
        $user = $this->createAuthUser();
        ResearchStaffFramework::create([
            'name' => 'Framework Especial',
            'description' => 'Descripción especial del framework',
            'start_year' => 2020
        ]);

        $response = $this->actingAs($user)->get(route('frameworks.index', ['search' => 'Especial']));

        $response->assertStatus(200);
        $response->assertViewHas('search', 'Especial');
    }

    /** @test */
    public function test_can_filter_by_year()
    {
        $user = $this->createAuthUser();

        $response = $this->actingAs($user)->get(route('frameworks.index', ['year' => 2023]));

        $response->assertStatus(200);
        $response->assertViewHas('year', 2023);
    }

    /** @test */
    public function test_pagination_works_correctly()
    {
        $user = $this->createAuthUser();

        $response = $this->actingAs($user)->get(route('frameworks.index', ['per_page' => 10]));

        $response->assertStatus(200);
        $response->assertViewHas('perPage', 10);
    }

    // ========================
    // CREATE TESTS
    // ========================

    /** @test */
    public function test_can_create_framework()
    {
        $user = $this->createAuthUser();
        $data = [
            'name' => 'Nuevo Framework',
            'description' => 'Descripción del nuevo framework válida',
            'link' => 'https://example.com',
            'start_year' => 2020,
            'end_year' => 2025
        ];

        $response = $this->actingAs($user)->post(route('frameworks.store'), $data);

        $response->assertRedirect(route('frameworks.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('frameworks', ['name' => 'Nuevo Framework']);
    }

    /** @test */
    public function test_validation_fails_with_missing_required_fields()
    {
        $user = $this->createAuthUser();
        $data = [];

        $response = $this->actingAs($user)->post(route('frameworks.store'), $data);

        $response->assertSessionHasErrors(['name', 'description', 'start_year']);
    }

    /** @test */
    public function test_validation_fails_with_short_description()
    {
        $user = $this->createAuthUser();
        $data = [
            'name' => 'Framework Test',
            'description' => 'Corta',
            'start_year' => 2020
        ];

        $response = $this->actingAs($user)->post(route('frameworks.store'), $data);

        $response->assertSessionHasErrors('description');
    }

    /** @test */
    public function test_validation_fails_with_duplicate_name()
    {
        $user = $this->createAuthUser();
        ResearchStaffFramework::create([
            'name' => 'Framework Existente',
            'description' => 'Descripción existente válida',
            'start_year' => 2020
        ]);

        $data = [
            'name' => 'Framework Existente',
            'description' => 'Nueva descripción válida',
            'start_year' => 2021
        ];

        $response = $this->actingAs($user)->post(route('frameworks.store'), $data);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function test_validation_fails_with_invalid_year()
    {
        $user = $this->createAuthUser();
        $data = [
            'name' => 'Framework Test',
            'description' => 'Descripción válida del framework',
            'start_year' => 1800
        ];

        $response = $this->actingAs($user)->post(route('frameworks.store'), $data);

        $response->assertSessionHasErrors('start_year');
    }

    /** @test */
    public function test_validation_fails_when_end_year_before_start_year()
    {
        $user = $this->createAuthUser();
        $data = [
            'name' => 'Framework Test',
            'description' => 'Descripción válida del framework',
            'start_year' => 2025,
            'end_year' => 2020
        ];

        $response = $this->actingAs($user)->post(route('frameworks.store'), $data);

        $response->assertSessionHasErrors('end_year');
    }

    // ========================
    // SHOW TESTS
    // ========================

    /** @test */
    public function test_can_show_framework()
    {
        $user = $this->createAuthUser();
        $framework = ResearchStaffFramework::create([
            'name' => 'Framework Test',
            'description' => 'Descripción del framework test',
            'start_year' => 2020
        ]);

        $response = $this->actingAs($user)->get(route('frameworks.show', $framework));

        $response->assertStatus(200);
        $response->assertViewIs('framework.show');
        $response->assertViewHas('framework');
    }

    /** @test */
    public function test_returns_404_for_nonexistent_framework()
    {
        $user = $this->createAuthUser();

        $response = $this->actingAs($user)->get(route('frameworks.show', 99999));

        $response->assertStatus(404);
    }

    /** @test */
    public function test_cannot_show_deleted_framework()
    {
        $user = $this->createAuthUser();
        $framework = ResearchStaffFramework::create([
            'name' => 'Framework Test',
            'description' => 'Descripción del framework test',
            'start_year' => 2020
        ]);
        $framework->delete();

        $response = $this->actingAs($user)->get(route('frameworks.show', $framework->id));

        $response->assertStatus(404);
    }

    // ========================
    // UPDATE TESTS
    // ========================

    /** @test */
    public function test_can_update_framework()
    {
        $user = $this->createAuthUser();
        $framework = ResearchStaffFramework::create([
            'name' => 'Framework Original',
            'description' => 'Descripción original válida',
            'start_year' => 2020
        ]);

        $data = [
            'name' => 'Framework Actualizado',
            'description' => 'Descripción actualizada válida',
            'start_year' => 2021,
            'end_year' => 2025
        ];

        $response = $this->actingAs($user)->put(route('frameworks.update', $framework), $data);

        $response->assertRedirect(route('frameworks.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('frameworks', ['name' => 'Framework Actualizado']);
    }

    /** @test */
    public function test_cannot_update_deleted_framework()
    {
        $user = $this->createAuthUser();
        $framework = ResearchStaffFramework::create([
            'name' => 'Framework Test',
            'description' => 'Descripción del framework test',
            'start_year' => 2020
        ]);
        $framework->delete();

        $data = [
            'name' => 'Framework Actualizado',
            'description' => 'Descripción actualizada válida',
            'start_year' => 2021
        ];

        $response = $this->actingAs($user)->put(route('frameworks.update', $framework->id), $data);

        $response->assertStatus(404);
    }

    // ========================
    // DELETE TESTS
    // ========================

    /** @test */
    public function test_can_soft_delete_framework()
    {
        $user = $this->createAuthUser();
        $framework = ResearchStaffFramework::create([
            'name' => 'Framework a Eliminar',
            'description' => 'Descripción del framework a eliminar',
            'start_year' => 2020
        ]);

        $response = $this->actingAs($user)->delete(route('frameworks.destroy', $framework));

        $response->assertRedirect(route('frameworks.index'));
        $response->assertSessionHas('success');
        $this->assertSoftDeleted('frameworks', ['id' => $framework->id]);
    }

    /** @test */
    public function test_cannot_delete_already_deleted_framework()
    {
        $user = $this->createAuthUser();
        $framework = ResearchStaffFramework::create([
            'name' => 'Framework Test',
            'description' => 'Descripción del framework test',
            'start_year' => 2020
        ]);
        $framework->delete();

        $response = $this->actingAs($user)->delete(route('frameworks.destroy', $framework->id));

        $response->assertStatus(404);
    }

    // ========================
    // RESTORE TESTS
    // ========================

    /** @test */
    public function test_can_restore_deleted_framework()
    {
        $user = $this->createAuthUser();
        $framework = ResearchStaffFramework::create([
            'name' => 'Framework a Restaurar',
            'description' => 'Descripción del framework a restaurar',
            'start_year' => 2020
        ]);
        $framework->delete();

        $response = $this->actingAs($user)->post(route('frameworks.restore', $framework->id));

        $response->assertRedirect(route('frameworks.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('frameworks', [
            'id' => $framework->id,
            'deleted_at' => null
        ]);
    }

    /** @test */
    public function test_cannot_restore_non_deleted_framework()
    {
        $user = $this->createAuthUser();
        $framework = ResearchStaffFramework::create([
            'name' => 'Framework Activo',
            'description' => 'Descripción del framework activo',
            'start_year' => 2020
        ]);

        $response = $this->actingAs($user)->post(route('frameworks.restore', $framework->id));

        $response->assertRedirect(route('frameworks.index'));
        $response->assertSessionHas('error');
    }

    // ========================
    // AUTHORIZATION TESTS
    // ========================

    /** @test */
    public function test_requires_authentication()
    {
        $response = $this->get(route('frameworks.index'));

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
