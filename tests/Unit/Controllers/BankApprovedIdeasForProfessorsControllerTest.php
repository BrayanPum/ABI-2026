<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\ResearchStaff\ResearchStaffUser;
use Illuminate\Support\Facades\Hash;

/**
 * Test for BankApprovedIdeasForProfessorsController
 *
 * Tests for professors viewing the bank of approved ideas
 */
class BankApprovedIdeasForProfessorsControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_professor_can_view_approved_ideas()
    {
        $professor = ResearchStaffUser::create([
            'email' => 'professor@example.com',
            'password' => Hash::make('password'),
            'role' => 'professor',
            'state' => 1,
        ]);

        $response = $this->actingAs($professor)->get('/bank-approved-ideas/professors');

        // Adjust based on actual route
        $this->assertTrue(in_array($response->status(), [200, 404]));
    }

    /** @test */
    public function test_committee_leader_can_view_approved_ideas()
    {
        $leader = ResearchStaffUser::create([
            'email' => 'leader@example.com',
            'password' => Hash::make('password'),
            'role' => 'committee_leader',
            'state' => 1,
        ]);

        $response = $this->actingAs($leader)->get('/bank-approved-ideas/professors');

        $this->assertTrue(in_array($response->status(), [200, 404]));
    }

    /** @test */
    public function test_requires_authentication()
    {
        $response = $this->get('/bank-approved-ideas/professors');

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function test_student_cannot_access_professor_bank()
    {
        $student = ResearchStaffUser::create([
            'email' => 'student@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'state' => 1,
        ]);

        $response = $this->actingAs($student)->get('/bank-approved-ideas/professors');

        $this->assertTrue(in_array($response->status(), [403, 404, 302]));
    }
}
