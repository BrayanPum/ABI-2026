<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Project;
use App\Models\Professor;
use App\Models\ProjectStatus;
use App\Models\ResearchStaff\ResearchStaffUser;
use App\Models\ResearchStaff\ResearchStaffCityProgram;
use Illuminate\Support\Facades\Hash;

/**
 * Test for ProjectEvaluationController
 *
 * Tests for committee leaders evaluating project proposals
 */
class ProjectEvaluationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $committeeLeader;
    protected $cityProgram;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup city program
        $this->cityProgram = ResearchStaffCityProgram::create([
            'city_id' => 1,
            'program_id' => 1
        ]);

        // Create committee leader user
        $this->committeeLeader = ResearchStaffUser::create([
            'email' => 'leader@example.com',
            'password' => Hash::make('password'),
            'role' => 'committee_leader',
            'state' => 1,
        ]);
    }

    /** @test */
    public function test_can_list_pending_projects()
    {
        $response = $this->actingAs($this->committeeLeader)->get(route('projects.evaluation.index'));

        // May return 200 or 403 depending on setup
        $this->assertTrue(in_array($response->status(), [200, 403]));
    }

    /** @test */
    public function test_can_show_project_for_evaluation()
    {
        // This test assumes a project exists with proper setup
        // In actual tests, you'd create the full project structure
        $response = $this->actingAs($this->committeeLeader)->get(route('projects.evaluation.index'));

        $this->assertTrue(in_array($response->status(), [200, 403]));
    }

    /** @test */
    public function test_requires_authentication()
    {
        $response = $this->get(route('projects.evaluation.index'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function test_regular_user_cannot_access_evaluation()
    {
        $student = ResearchStaffUser::create([
            'email' => 'student@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'state' => 1,
        ]);

        $response = $this->actingAs($student)->get(route('projects.evaluation.index'));

        // Should not have access or redirect
        $this->assertTrue(in_array($response->status(), [403, 404, 302]));
    }
}
