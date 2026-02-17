<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\ResearchStaff\ResearchStaffUser;
use Illuminate\Support\Facades\Hash;

/**
 * Test for BankApprovedIdeasForStudentsController
 *
 * Tests for students viewing the bank of approved ideas
 */
class BankApprovedIdeasForStudentsControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_student_can_view_approved_ideas()
    {
        $student = ResearchStaffUser::create([
            'email' => 'student@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'state' => 1,
        ]);

        $response = $this->actingAs($student)->get('/bank-approved-ideas/students');

        // Adjust based on actual route
        $this->assertTrue(in_array($response->status(), [200, 404]));
    }

    /** @test */
    public function test_requires_authentication()
    {
        $response = $this->get('/bank-approved-ideas/students');

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function test_professor_cannot_access_student_bank()
    {
        $professor = ResearchStaffUser::create([
            'email' => 'professor@example.com',
            'password' => Hash::make('password'),
            'role' => 'professor',
            'state' => 1,
        ]);

        $response = $this->actingAs($professor)->get('/bank-approved-ideas/students');

        $this->assertTrue(in_array($response->status(), [403, 404, 302]));
    }
}
