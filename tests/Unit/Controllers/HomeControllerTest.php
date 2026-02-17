<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\ResearchStaff\ResearchStaffUser;
use Illuminate\Support\Facades\Hash;

class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_can_view_home_page()
    {
        $user = ResearchStaffUser::create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'state' => 1,
        ]);

        $response = $this->actingAs($user)->get(route('home'));

        $response->assertStatus(200);
        $response->assertViewIs('home');
    }

    /** @test */
    public function test_requires_authentication()
    {
        $response = $this->get(route('home'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function test_authenticated_student_can_access_home()
    {
        $user = ResearchStaffUser::create([
            'email' => 'student@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'state' => 1,
        ]);

        $response = $this->actingAs($user)->get(route('home'));

        $response->assertStatus(200);
    }

    /** @test */
    public function test_authenticated_professor_can_access_home()
    {
        $user = ResearchStaffUser::create([
            'email' => 'professor@example.com',
            'password' => Hash::make('password'),
            'role' => 'professor',
            'state' => 1,
        ]);

        $response = $this->actingAs($user)->get(route('home'));

        $response->assertStatus(200);
    }

    /** @test */
    public function test_authenticated_research_staff_can_access_home()
    {
        $user = ResearchStaffUser::create([
            'email' => 'staff@example.com',
            'password' => Hash::make('password'),
            'role' => 'research_staff',
            'state' => 1,
        ]);

        $response = $this->actingAs($user)->get(route('home'));

        $response->assertStatus(200);
    }
}
