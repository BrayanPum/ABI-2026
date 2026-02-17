<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\ResearchStaff\ResearchStaffDepartment;
use App\Models\ResearchStaff\ResearchStaffUser;
use Illuminate\Support\Facades\Hash;

class DepartmentControllerTest extends TestCase
{
    use RefreshDatabase;

    // ========================
    // INDEX TESTS
    // ========================

    /** @test */
    public function test_can_list_departments()
    {
        $user = $this->createAuthUser();
        ResearchStaffDepartment::create(['name' => 'Departamento Test']);

        $response = $this->actingAs($user)->get(route('departments.index'));

        $response->assertStatus(200);
        $response->assertViewIs('departments.index');
        $response->assertViewHas('departments');
    }

    /** @test */
    public function test_can_search_departments()
    {
        $user = $this->createAuthUser();
        ResearchStaffDepartment::create(['name' => 'Departamento Especial']);

        $response = $this->actingAs($user)->get(route('departments.index', ['search' => 'Especial']));

        $response->assertStatus(200);
        $response->assertViewHas('search', 'Especial');
    }

    /** @test */
    public function test_pagination_works_correctly()
    {
        $user = $this->createAuthUser();

        $response = $this->actingAs($user)->get(route('departments.index', ['per_page' => 10]));

        $response->assertStatus(200);
        $response->assertViewHas('perPage', 10);
    }

    // ========================
    // CREATE TESTS
    // ========================

    /** @test */
    public function test_can_create_department()
    {
        $user = $this->createAuthUser();
        $data = ['name' => 'Nuevo Departamento'];

        $response = $this->actingAs($user)->post(route('departments.store'), $data);

        $response->assertRedirect(route('departments.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('departments', ['name' => 'Nuevo Departamento']);
    }

    /** @test */
    public function test_validation_fails_with_missing_name()
    {
        $user = $this->createAuthUser();
        $data = [];

        $response = $this->actingAs($user)->post(route('departments.store'), $data);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function test_validation_fails_with_duplicate_name()
    {
        $user = $this->createAuthUser();
        ResearchStaffDepartment::create(['name' => 'Departamento Existente']);

        $data = ['name' => 'Departamento Existente'];

        $response = $this->actingAs($user)->post(route('departments.store'), $data);

        $response->assertSessionHasErrors('name');
    }

    // ========================
    // SHOW TESTS
    // ========================

    /** @test */
    public function test_can_show_department()
    {
        $user = $this->createAuthUser();
        $department = ResearchStaffDepartment::create(['name' => 'Departamento Test']);

        $response = $this->actingAs($user)->get(route('departments.show', $department));

        $response->assertStatus(200);
        $response->assertViewIs('departments.show');
        $response->assertViewHas('department');
    }

    /** @test */
    public function test_returns_404_for_nonexistent_department()
    {
        $user = $this->createAuthUser();

        $response = $this->actingAs($user)->get(route('departments.show', 99999));

        $response->assertStatus(404);
    }

    // ========================
    // UPDATE TESTS
    // ========================

    /** @test */
    public function test_can_update_department()
    {
        $user = $this->createAuthUser();
        $department = ResearchStaffDepartment::create(['name' => 'Departamento Original']);

        $data = ['name' => 'Departamento Actualizado'];

        $response = $this->actingAs($user)->put(route('departments.update', $department), $data);

        $response->assertRedirect(route('departments.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('departments', ['name' => 'Departamento Actualizado']);
    }

    /** @test */
    public function test_validation_fails_when_updating_with_duplicate_name()
    {
        $user = $this->createAuthUser();
        ResearchStaffDepartment::create(['name' => 'Departamento Existente']);
        $department = ResearchStaffDepartment::create(['name' => 'Departamento Test']);

        $data = ['name' => 'Departamento Existente'];

        $response = $this->actingAs($user)->put(route('departments.update', $department), $data);

        $response->assertSessionHasErrors('name');
    }

    // ========================
    // DELETE TESTS
    // ========================

    /** @test */
    public function test_can_delete_department()
    {
        $user = $this->createAuthUser();
        $department = ResearchStaffDepartment::create(['name' => 'Departamento a Eliminar']);

        $response = $this->actingAs($user)->delete(route('departments.destroy', $department));

        $response->assertRedirect(route('departments.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('departments', ['id' => $department->id]);
    }

    // ========================
    // AUTHORIZATION TESTS
    // ========================

    /** @test */
    public function test_requires_authentication()
    {
        $response = $this->get(route('departments.index'));

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
