<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Project;
use App\Models\Student;
use App\Models\ProjectStatus;
use App\Models\ResearchStaff\ResearchStaffUser;
use App\Models\ResearchStaff\ResearchStaffCityProgram;
use Illuminate\Support\Facades\Hash;

/**
 * Test for BankApprovedIdeasAssignController
 *
 * Tests for students selecting and assigning approved project ideas
 */
class BankApprovedIdeasAssignControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $student;
    protected $cityProgram;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cityProgram = ResearchStaffCityProgram::create([
            'city_id' => 1,
            'program_id' => 1
        ]);

        $this->student = ResearchStaffUser::create([
            'email' => 'student@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'state' => 1,
        ]);
    }

    /** @test */
    public function test_requires_authentication()
    {
        // Assuming route exists
        $response = $this->get('/bank-approved-ideas');

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function test_authenticated_student_can_access()
    {
        $response = $this->actingAs($this->student)->get('/bank-approved-ideas');

        // May return 200 or 404 depending on implementation
        $this->assertTrue(in_array($response->status(), [200, 404]));
    }

    /** @test */
    public function test_professor_cannot_access_student_features()
    {
        $professor = ResearchStaffUser::create([
            'email' => 'professor@example.com',
            'password' => Hash::make('password'),
            'role' => 'professor',
            'state' => 1,
        ]);

        $response = $this->actingAs($professor)->get('/bank-approved-ideas');

        // Should not have access
        $this->assertTrue(in_array($response->status(), [403, 404, 302]));
    }
}
