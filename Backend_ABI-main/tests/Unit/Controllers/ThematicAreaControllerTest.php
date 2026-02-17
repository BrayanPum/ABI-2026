<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\ResearchStaff\ResearchStaffThematicArea;
use App\Models\ResearchStaff\ResearchStaffInvestigationLine;
use App\Models\ResearchStaff\ResearchStaffResearchGroup;
use App\Models\ResearchStaff\ResearchStaffUser;
use Illuminate\Support\Facades\Hash;

class ThematicAreaControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $investigationLine;

    protected function setUp(): void
    {
        parent::setUp();
        $researchGroup = ResearchStaffResearchGroup::create([
            'name' => 'Grupo Test',
            'initials' => 'GT',
            'description' => 'Descripción del grupo test'
        ]);
        $this->investigationLine = ResearchStaffInvestigationLine::create([
            'name' => 'Línea Test',
            'description' => 'Descripción de la línea',
            'research_group_id' => $researchGroup->id
        ]);
    }

    /** @test */
    public function test_can_list_thematic_areas()
    {
        $user = $this->createAuthUser();
        ResearchStaffThematicArea::create([
            'name' => 'Área Temática Test',
            'description' => 'Descripción del área',
            'investigation_line_id' => $this->investigationLine->id
        ]);

        $response = $this->actingAs($user)->get(route('thematic-areas.index'));

        $response->assertStatus(200);
        $response->assertViewIs('thematic-areas.index');
        $response->assertViewHas('thematicAreas');
    }

    /** @test */
    public function test_can_create_thematic_area()
    {
        $user = $this->createAuthUser();
        $data = [
            'name' => 'Nueva Área Temática',
            'description' => 'Descripción de la nueva área',
            'investigation_line_id' => $this->investigationLine->id
        ];

        $response = $this->actingAs($user)->post(route('thematic-areas.store'), $data);

        $response->assertRedirect(route('thematic-areas.index'));
        $this->assertDatabaseHas('thematic_areas', ['name' => 'Nueva Área Temática']);
    }

    /** @test */
    public function test_validation_fails_with_duplicate_name()
    {
        $user = $this->createAuthUser();
        ResearchStaffThematicArea::create([
            'name' => 'Área Existente',
            'description' => 'Descripción existente',
            'investigation_line_id' => $this->investigationLine->id
        ]);

        $data = [
            'name' => 'Área Existente',
            'description' => 'Nueva descripción',
            'investigation_line_id' => $this->investigationLine->id
        ];

        $response = $this->actingAs($user)->post(route('thematic-areas.store'), $data);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function test_can_update_thematic_area()
    {
        $user = $this->createAuthUser();
        $area = ResearchStaffThematicArea::create([
            'name' => 'Área Original',
            'description' => 'Descripción original',
            'investigation_line_id' => $this->investigationLine->id
        ]);

        $data = [
            'name' => 'Área Actualizada',
            'description' => 'Descripción actualizada',
            'investigation_line_id' => $this->investigationLine->id
        ];

        $response = $this->actingAs($user)->put(route('thematic-areas.update', $area), $data);

        $response->assertRedirect(route('thematic-areas.index'));
        $this->assertDatabaseHas('thematic_areas', ['name' => 'Área Actualizada']);
    }

    /** @test */
    public function test_can_delete_thematic_area()
    {
        $user = $this->createAuthUser();
        $area = ResearchStaffThematicArea::create([
            'name' => 'Área Test',
            'description' => 'Descripción test',
            'investigation_line_id' => $this->investigationLine->id
        ]);

        $response = $this->actingAs($user)->delete(route('thematic-areas.destroy', $area));

        $response->assertRedirect(route('thematic-areas.index'));
        $this->assertSoftDeleted('thematic_areas', ['id' => $area->id]);
    }

    /** @test */
    public function test_returns_404_for_nonexistent_area()
    {
        $user = $this->createAuthUser();

        $response = $this->actingAs($user)->get(route('thematic-areas.show', 99999));

        $response->assertStatus(404);
    }

    /** @test */
    public function test_requires_authentication()
    {
        $response = $this->get(route('thematic-areas.index'));

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
