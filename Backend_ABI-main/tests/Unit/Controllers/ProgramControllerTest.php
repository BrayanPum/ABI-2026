<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\ResearchStaff\ResearchStaffProgram;
use App\Models\ResearchStaff\ResearchStaffResearchGroup;
use App\Models\ResearchStaff\ResearchStaffUser;
use Illuminate\Support\Facades\Hash;

class ProgramControllerTest extends TestCase
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

    /** @test */
    public function test_can_list_programs()
    {
        $user = $this->createAuthUser();
        ResearchStaffProgram::create([
            'code' => 1001,
            'name' => 'Programa Test',
            'research_group_id' => $this->researchGroup->id
        ]);

        $response = $this->actingAs($user)->get(route('programs.index'));

        $response->assertStatus(200);
        $response->assertViewIs('programs.index');
        $response->assertViewHas('programs');
    }

    /** @test */
    public function test_can_create_program()
    {
        $user = $this->createAuthUser();
        $data = [
            'code' => 1001,
            'name' => 'Nuevo Programa',
            'research_group_id' => $this->researchGroup->id
        ];

        $response = $this->actingAs($user)->post(route('programs.store'), $data);

        $response->assertRedirect(route('programs.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('programs', ['name' => 'Nuevo Programa']);
    }

    /** @test */
    public function test_validation_fails_with_duplicate_code()
    {
        $user = $this->createAuthUser();
        ResearchStaffProgram::create([
            'code' => 1001,
            'name' => 'Programa Existente',
            'research_group_id' => $this->researchGroup->id
        ]);

        $data = [
            'code' => 1001,
            'name' => 'Nuevo Programa',
            'research_group_id' => $this->researchGroup->id
        ];

        $response = $this->actingAs($user)->post(route('programs.store'), $data);

        $response->assertSessionHasErrors('code');
    }

    /** @test */
    public function test_can_update_program()
    {
        $user = $this->createAuthUser();
        $program = ResearchStaffProgram::create([
            'code' => 1001,
            'name' => 'Programa Original',
            'research_group_id' => $this->researchGroup->id
        ]);

        $data = [
            'code' => 1002,
            'name' => 'Programa Actualizado',
            'research_group_id' => $this->researchGroup->id
        ];

        $response = $this->actingAs($user)->put(route('programs.update', $program), $data);

        $response->assertRedirect(route('programs.index'));
        $this->assertDatabaseHas('programs', ['name' => 'Programa Actualizado']);
    }

    /** @test */
    public function test_can_soft_delete_program()
    {
        $user = $this->createAuthUser();
        $program = ResearchStaffProgram::create([
            'code' => 1001,
            'name' => 'Programa Test',
            'research_group_id' => $this->researchGroup->id
        ]);

        $response = $this->actingAs($user)->delete(route('programs.destroy', $program));

        $response->assertRedirect(route('programs.index'));
        $this->assertSoftDeleted('programs', ['id' => $program->id]);
    }

    /** @test */
    public function test_can_restore_deleted_program()
    {
        $user = $this->createAuthUser();
        $program = ResearchStaffProgram::create([
            'code' => 1001,
            'name' => 'Programa Test',
            'research_group_id' => $this->researchGroup->id
        ]);
        $program->delete();

        $response = $this->actingAs($user)->post(route('programs.restore', $program->id));

        $response->assertRedirect(route('programs.index'));
        $this->assertDatabaseHas('programs', ['id' => $program->id, 'deleted_at' => null]);
    }

    /** @test */
    public function test_returns_404_for_nonexistent_program()
    {
        $user = $this->createAuthUser();

        $response = $this->actingAs($user)->get(route('programs.show', 99999));

        $response->assertStatus(404);
    }

    /** @test */
    public function test_requires_authentication()
    {
        $response = $this->get(route('programs.index'));

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
