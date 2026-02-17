<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\ResearchStaff\ResearchStaffCity;
use App\Models\ResearchStaff\ResearchStaffDepartment;
use App\Models\ResearchStaff\ResearchStaffUser;
use Illuminate\Support\Facades\Hash;

class CityControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $department;

    protected function setUp(): void
    {
        parent::setUp();
        $this->department = ResearchStaffDepartment::create(['name' => 'Departamento Test']);
    }

    // ========================
    // INDEX TESTS
    // ========================

    /** @test */
    public function test_can_list_cities()
    {
        $user = $this->createAuthUser();
        ResearchStaffCity::create([
            'name' => 'Ciudad Test',
            'department_id' => $this->department->id
        ]);

        $response = $this->actingAs($user)->get(route('cities.index'));

        $response->assertStatus(200);
        $response->assertViewIs('city.index');
        $response->assertViewHas('cities');
    }

    /** @test */
    public function test_can_search_cities()
    {
        $user = $this->createAuthUser();
        ResearchStaffCity::create([
            'name' => 'Ciudad Especial',
            'department_id' => $this->department->id
        ]);

        $response = $this->actingAs($user)->get(route('cities.index', ['search' => 'Especial']));

        $response->assertStatus(200);
        $response->assertViewHas('search', 'Especial');
    }

    /** @test */
    public function test_can_filter_by_department()
    {
        $user = $this->createAuthUser();

        $response = $this->actingAs($user)->get(route('cities.index', ['department_id' => $this->department->id]));

        $response->assertStatus(200);
        $response->assertViewHas('departmentId', $this->department->id);
    }

    /** @test */
    public function test_pagination_works_correctly()
    {
        $user = $this->createAuthUser();

        $response = $this->actingAs($user)->get(route('cities.index', ['per_page' => 10]));

        $response->assertStatus(200);
        $response->assertViewHas('perPage', 10);
    }

    // ========================
    // CREATE TESTS
    // ========================

    /** @test */
    public function test_can_create_city()
    {
        $user = $this->createAuthUser();
        $data = [
            'name' => 'Nueva Ciudad',
            'department_id' => $this->department->id
        ];

        $response = $this->actingAs($user)->post(route('cities.store'), $data);

        $response->assertRedirect(route('cities.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('cities', ['name' => 'Nueva Ciudad']);
    }

    /** @test */
    public function test_validation_fails_with_missing_fields()
    {
        $user = $this->createAuthUser();
        $data = [];

        $response = $this->actingAs($user)->post(route('cities.store'), $data);

        $response->assertSessionHasErrors(['name', 'department_id']);
    }

    /** @test */
    public function test_validation_fails_with_duplicate_name()
    {
        $user = $this->createAuthUser();
        ResearchStaffCity::create([
            'name' => 'Ciudad Existente',
            'department_id' => $this->department->id
        ]);

        $data = [
            'name' => 'Ciudad Existente',
            'department_id' => $this->department->id
        ];

        $response = $this->actingAs($user)->post(route('cities.store'), $data);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function test_validation_fails_with_invalid_department()
    {
        $user = $this->createAuthUser();
        $data = [
            'name' => 'Nueva Ciudad',
            'department_id' => 99999
        ];

        $response = $this->actingAs($user)->post(route('cities.store'), $data);

        $response->assertSessionHasErrors('department_id');
    }

    // ========================
    // SHOW TESTS
    // ========================

    /** @test */
    public function test_can_show_city()
    {
        $user = $this->createAuthUser();
        $city = ResearchStaffCity::create([
            'name' => 'Ciudad Test',
            'department_id' => $this->department->id
        ]);

        $response = $this->actingAs($user)->get(route('cities.show', $city));

        $response->assertStatus(200);
        $response->assertViewIs('city.show');
        $response->assertViewHas('city');
    }

    /** @test */
    public function test_returns_404_for_nonexistent_city()
    {
        $user = $this->createAuthUser();

        $response = $this->actingAs($user)->get(route('cities.show', 99999));

        $response->assertStatus(404);
    }

    // ========================
    // UPDATE TESTS
    // ========================

    /** @test */
    public function test_can_update_city()
    {
        $user = $this->createAuthUser();
        $city = ResearchStaffCity::create([
            'name' => 'Ciudad Original',
            'department_id' => $this->department->id
        ]);

        $data = [
            'name' => 'Ciudad Actualizada',
            'department_id' => $this->department->id
        ];

        $response = $this->actingAs($user)->put(route('cities.update', $city), $data);

        $response->assertRedirect(route('cities.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('cities', ['name' => 'Ciudad Actualizada']);
    }

    // ========================
    // DELETE TESTS
    // ========================

    /** @test */
    public function test_can_delete_city()
    {
        $user = $this->createAuthUser();
        $city = ResearchStaffCity::create([
            'name' => 'Ciudad a Eliminar',
            'department_id' => $this->department->id
        ]);

        $response = $this->actingAs($user)->delete(route('cities.destroy', $city));

        $response->assertRedirect(route('cities.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('cities', ['id' => $city->id]);
    }

    // ========================
    // AUTHORIZATION TESTS
    // ========================

    /** @test */
    public function test_requires_authentication()
    {
        $response = $this->get(route('cities.index'));

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
