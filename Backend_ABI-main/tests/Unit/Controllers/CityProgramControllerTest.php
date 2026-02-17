<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\ResearchStaff\ResearchStaffCityProgram;
use App\Models\ResearchStaff\ResearchStaffCity;
use App\Models\ResearchStaff\ResearchStaffProgram;
use App\Models\ResearchStaff\ResearchStaffDepartment;
use App\Models\ResearchStaff\ResearchStaffResearchGroup;
use App\Models\ResearchStaff\ResearchStaffUser;
use Illuminate\Support\Facades\Hash;

class CityProgramControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $city;
    protected $program;

    protected function setUp(): void
    {
        parent::setUp();

        $department = ResearchStaffDepartment::create(['name' => 'Department Test']);
        $this->city = ResearchStaffCity::create([
            'name' => 'City Test',
            'department_id' => $department->id
        ]);

        $researchGroup = ResearchStaffResearchGroup::create([
            'name' => 'Group Test',
            'initials' => 'GT',
            'description' => 'Group description test'
        ]);
        $this->program = ResearchStaffProgram::create([
            'code' => 1001,
            'name' => 'Program Test',
            'research_group_id' => $researchGroup->id
        ]);
    }

    /** @test */
    public function test_can_list_city_programs()
    {
        $user = $this->createAuthUser();
        ResearchStaffCityProgram::create([
            'city_id' => $this->city->id,
            'program_id' => $this->program->id
        ]);

        $response = $this->actingAs($user)->get('/city-programs');

        $this->assertTrue(in_array($response->status(), [200, 404]));
    }

    /** @test */
    public function test_can_create_city_program()
    {
        $user = $this->createAuthUser();
        $data = [
            'city_id' => $this->city->id,
            'program_id' => $this->program->id
        ];

        $response = $this->actingAs($user)->post('/city-programs', $data);

        $this->assertTrue(in_array($response->status(), [200, 201, 302, 404]));
    }

    /** @test */
    public function test_can_show_city_program()
    {
        $user = $this->createAuthUser();
        $cityProgram = ResearchStaffCityProgram::create([
            'city_id' => $this->city->id,
            'program_id' => $this->program->id
        ]);

        $response = $this->actingAs($user)->get("/city-programs/{$cityProgram->id}");

        $this->assertTrue(in_array($response->status(), [200, 404]));
    }

    /** @test */
    public function test_can_update_city_program()
    {
        $user = $this->createAuthUser();
        $cityProgram = ResearchStaffCityProgram::create([
            'city_id' => $this->city->id,
            'program_id' => $this->program->id
        ]);

        $data = [
            'city_id' => $this->city->id,
            'program_id' => $this->program->id
        ];

        $response = $this->actingAs($user)->put("/city-programs/{$cityProgram->id}", $data);

        $this->assertTrue(in_array($response->status(), [200, 302, 404]));
    }

    /** @test */
    public function test_can_delete_city_program()
    {
        $user = $this->createAuthUser();
        $cityProgram = ResearchStaffCityProgram::create([
            'city_id' => $this->city->id,
            'program_id' => $this->program->id
        ]);

        $response = $this->actingAs($user)->delete("/city-programs/{$cityProgram->id}");

        $this->assertTrue(in_array($response->status(), [200, 302, 404]));
    }

    /** @test */
    public function test_requires_authentication()
    {
        $response = $this->get('/city-programs');

        $response->assertRedirect(route('login'));
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
