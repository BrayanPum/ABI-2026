<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\CityProgram;
use App\Models\Department;
use App\Models\InvestigationLine;
use App\Models\Professor;
use App\Models\Program;
use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\ResearchGroup;
use App\Models\Student;
use App\Models\ThematicArea;
use App\Models\User;
use App\Models\Version;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProjectApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_project_with_assignments(): void
    {
        $catalog = $this->createProjectCatalog();
        $professor = $this->createProfessor($catalog['cityProgram']->id);
        $student = $this->createStudent($catalog['cityProgram']->id);

        $payload = [
            'title' => 'Proyecto integrador',
            'evaluation_criteria' => 'Cumplir cada fase definida en el plan de trabajo.',
            'thematic_area_id' => $catalog['thematicArea']->id,
            'project_status_id' => $catalog['status']->id,
            'professor_ids' => [$professor->id],
            'student_ids' => [$student->id],
        ];

        $response = $this->postJson('/api/projects', $payload);

        $response
            ->assertCreated()
            ->assertJsonFragment(['title' => 'Proyecto Integrador'])
            ->assertJsonPath('data.professors.0.id', $professor->id)
            ->assertJsonPath('data.students.0.id', $student->id);

        $this->assertDatabaseHas('projects', [
            'title' => 'Proyecto Integrador',
            'thematic_area_id' => $catalog['thematicArea']->id,
            'project_status_id' => $catalog['status']->id,
        ]);

        $this->assertDatabaseHas('professor_project', [
            'professor_id' => $professor->id,
        ]);

        $this->assertDatabaseHas('student_project', [
            'student_id' => $student->id,
        ]);
    }

    public function test_can_update_project_and_sync_assignments(): void
    {
        $catalog = $this->createProjectCatalog();
        $professorA = $this->createProfessor($catalog['cityProgram']->id);
        $professorB = $this->createProfessor($catalog['cityProgram']->id);
        $studentA = $this->createStudent($catalog['cityProgram']->id);
        $studentB = $this->createStudent($catalog['cityProgram']->id);

        $project = Project::create([
            'title' => 'Proyecto base',
            'evaluation_criteria' => 'Criterios iniciales',
            'thematic_area_id' => $catalog['thematicArea']->id,
            'project_status_id' => $catalog['status']->id,
        ]);
        $project->professors()->sync([$professorA->id]);
        $project->students()->sync([$studentA->id]);

        $updatePayload = [
            'title' => 'Proyecto actualizado',
            'evaluation_criteria' => 'Rubrica final',
            'thematic_area_id' => $catalog['thematicArea']->id,
            'project_status_id' => $catalog['status']->id,
            'professor_ids' => [$professorB->id],
            'student_ids' => [$studentB->id],
        ];

        $response = $this->putJson('/api/projects/' . $project->id, $updatePayload);

        $response
            ->assertOk()
            ->assertJsonFragment(['title' => 'Proyecto Actualizado'])
            ->assertJsonPath('data.professors.0.id', $professorB->id)
            ->assertJsonPath('data.students.0.id', $studentB->id);

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'title' => 'Proyecto Actualizado',
            'evaluation_criteria' => 'Rubrica final',
        ]);

        $this->assertDatabaseMissing('professor_project', [
            'project_id' => $project->id,
            'professor_id' => $professorA->id,
        ]);

        $this->assertDatabaseHas('professor_project', [
            'project_id' => $project->id,
            'professor_id' => $professorB->id,
        ]);
    }

    public function test_index_allows_filtering_by_professor_and_student(): void
    {
        $catalog = $this->createProjectCatalog();
        $professorA = $this->createProfessor($catalog['cityProgram']->id);
        $professorB = $this->createProfessor($catalog['cityProgram']->id);
        $studentA = $this->createStudent($catalog['cityProgram']->id);
        $studentB = $this->createStudent($catalog['cityProgram']->id);

        $project1 = Project::create([
            'title' => 'Proyecto uno',
            'evaluation_criteria' => 'Rúbrica A',
            'thematic_area_id' => $catalog['thematicArea']->id,
            'project_status_id' => $catalog['status']->id,
        ]);
        $project1->professors()->sync([$professorA->id]);
        $project1->students()->sync([$studentA->id]);

        $project2 = Project::create([
            'title' => 'Proyecto dos',
            'evaluation_criteria' => 'Rúbrica B',
            'thematic_area_id' => $catalog['thematicArea']->id,
            'project_status_id' => $catalog['status']->id,
        ]);
        $project2->professors()->sync([$professorB->id]);
        $project2->students()->sync([$studentB->id]);

        $response = $this->getJson('/api/projects?professor_id=' . $professorB->id . '&student_id=' . $studentB->id);

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $project2->id);
    }

    public function test_prevents_deleting_project_with_versions(): void
    {
        $catalog = $this->createProjectCatalog();
        $project = Project::create([
            'title' => 'Proyecto con versión',
            'evaluation_criteria' => 'Evaluación continua',
            'thematic_area_id' => $catalog['thematicArea']->id,
            'project_status_id' => $catalog['status']->id,
        ]);
        Version::create(['project_id' => $project->id]);

        $response = $this->deleteJson('/api/projects/' . $project->id);

        $response->assertStatus(409);
        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'deleted_at' => null,
        ]);
    }

    public function test_can_soft_delete_and_restore_project(): void
    {
        $catalog = $this->createProjectCatalog();
        $project = Project::create([
            'title' => 'Proyecto temporal',
            'evaluation_criteria' => 'Revisión interna',
            'thematic_area_id' => $catalog['thematicArea']->id,
            'project_status_id' => $catalog['status']->id,
        ]);

        $deleteResponse = $this->deleteJson('/api/projects/' . $project->id);
        $deleteResponse->assertNoContent();

        $this->assertSoftDeleted('projects', ['id' => $project->id]);

        $restoreResponse = $this->postJson('/api/projects/' . $project->id . '/restore');
        $restoreResponse
            ->assertOk()
            ->assertJsonPath('data.id', $project->id);

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'deleted_at' => null,
        ]);
    }

    public function test_meta_endpoint_returns_catalog_data(): void
    {
        $catalog = $this->createProjectCatalog();
        $professor = $this->createProfessor($catalog['cityProgram']->id);
        $student = $this->createStudent($catalog['cityProgram']->id);

        $response = $this->getJson('/api/projects/meta');

        $response
            ->assertOk()
            ->assertJsonFragment(['name' => $catalog['thematicArea']->name])
            ->assertJsonFragment(['name' => $catalog['status']->name])
            ->assertJsonFragment(['card_id' => $professor->card_id])
            ->assertJsonFragment(['card_id' => $student->card_id]);
    }

    public function test_index_can_include_or_only_deleted_projects(): void
    {
        $catalog = $this->createProjectCatalog();

        $activeProject = Project::create([
            'title' => 'Proyecto vigente',
            'thematic_area_id' => $catalog['thematicArea']->id,
            'project_status_id' => $catalog['status']->id,
        ]);

        $deletedProject = Project::create([
            'title' => 'Proyecto eliminado',
            'thematic_area_id' => $catalog['thematicArea']->id,
            'project_status_id' => $catalog['status']->id,
        ]);
        $deletedProject->delete();

        $withDeleted = $this->getJson('/api/projects?include_deleted=1');
        $withDeleted
            ->assertOk()
            ->assertJsonFragment(['id' => $activeProject->id])
            ->assertJsonFragment(['id' => $deletedProject->id]);

        $onlyDeleted = $this->getJson('/api/projects?only_deleted=1');
        $onlyDeleted
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $deletedProject->id);
    }

    private function createProjectCatalog(): array
    {
        $department = Department::create(['name' => 'Antioquia']);
        $city = City::create([
            'name' => 'Medellín',
            'department_id' => $department->id,
        ]);

        $researchGroup = ResearchGroup::create([
            'name' => 'Grupo Pedagógico',
            'initials' => 'GP',
            'description' => 'Grupo dedicado a proyectos educativos bilingües.',
        ]);

        $program = Program::create([
            'code' => random_int(1000, 9999),
            'name' => 'Licenciatura en Educación',
            'research_group_id' => $researchGroup->id,
        ]);

        $cityProgram = CityProgram::create([
            'city_id' => $city->id,
            'program_id' => $program->id,
        ]);

        $investigationLine = InvestigationLine::create([
            'name' => 'Innovación educativa',
            'description' => 'Líneas de innovación y bilingüismo.',
            'research_group_id' => $researchGroup->id,
        ]);

        $thematicArea = ThematicArea::create([
            'name' => 'Tecnologías aplicadas',
            'description' => 'Uso de tecnologías para potenciar el aprendizaje.',
            'investigation_line_id' => $investigationLine->id,
        ]);

        $status = new ProjectStatus();
        $status->name = 'En revisión';
        $status->description = 'Proyecto pendiente de evaluación.';
        $status->save();

        return [
            'department' => $department,
            'city' => $city,
            'researchGroup' => $researchGroup,
            'program' => $program,
            'cityProgram' => $cityProgram,
            'investigationLine' => $investigationLine,
            'thematicArea' => $thematicArea,
            'status' => $status,
        ];
    }

    private function createProfessor(int $cityProgramId): Professor
    {
        $user = User::create([
            'email' => uniqid('prof_') . '@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'professor',
        ]);

        $professor = Professor::create([
            'card_id' => uniqid('PRF'),
            'name' => 'Laura',
            'last_name' => 'García',
            'phone' => '3001234567',
            'city_program_id' => $cityProgramId,
            'user_id' => $user->id,
        ]);
        $professor->committee_leader = false;
        $professor->save();

        return $professor->fresh();
    }

    private function createStudent(int $cityProgramId): Student
    {
        $user = User::create([
            'email' => uniqid('stud_') . '@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'student',
        ]);

        return Student::create([
            'card_id' => uniqid('STD'),
            'name' => 'Carlos',
            'last_name' => 'Pérez',
            'phone' => '3017654321',
            'semester' => 6,
            'city_program_id' => $cityProgramId,
            'user_id' => $user->id,
        ]);
    }
}
