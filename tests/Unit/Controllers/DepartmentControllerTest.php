<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\ResearchStaff\ResearchStaffCity;
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

        $response->assertRedirect(route('departments-cities.index'));
    }

    /** @test */
    public function test_can_search_departments()
    {
        $user = $this->createAuthUser();
        ResearchStaffDepartment::create(['name' => 'Departamento Especial']);

        $response = $this->actingAs($user)->get(route('departments.index', ['search' => 'Especial']));

        $response->assertRedirect(route('departments-cities.index', ['department_search' => 'Especial']));
    }

    /** @test */
    public function test_can_render_unified_departments_and_cities_view()
    {
        $user = $this->createAuthUser();
        $department = ResearchStaffDepartment::create(['name' => 'Departamento Central']);
        ResearchStaffCity::create([
            'name' => 'Ciudad Central',
            'department_id' => $department->id,
        ]);

        $response = $this->actingAs($user)->get(route('departments-cities.index', [
            'selected_department_id' => $department->id,
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('departments.unified-index');
        $response->assertViewHas('selectedDepartment');
        $response->assertViewHas('cities');
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
    public function test_can_create_department_and_return_to_unified_view()
    {
        $user = $this->createAuthUser();

        $response = $this->actingAs($user)->post(route('departments.store'), [
            'name' => 'Departamento Unificado',
            'redirect_to' => '/departments-cities',
        ]);

        $response->assertRedirect('/departments-cities');
        $this->assertDatabaseHas('departments', ['name' => 'Departamento Unificado']);
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

        $response->assertRedirect(route('departments-cities.index', [
            'selected_department_id' => $department->id,
        ]));
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
