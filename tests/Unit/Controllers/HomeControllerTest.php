<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use App\Models\ResearchStaff\ResearchStaffResearchStaff;
use App\Models\ResearchStaff\ResearchStaffUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

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

    /** @test */
    public function test_home_reuses_authenticated_user_name_in_dashboard_and_header()
    {
        $user = ResearchStaffUser::create([
            'email' => 'carlos@example.com',
            'password' => Hash::make('password'),
            'role' => 'research_staff',
            'state' => 1,
        ]);

        ResearchStaffResearchStaff::create([
            'user_id' => $user->id,
            'card_id' => 'RS-1001',
            'name' => 'Carlos',
            'last_name' => 'Montoya',
            'phone' => '3001234567',
        ]);

        $response = $this->actingAs($user)->get(route('home'));

        $response->assertStatus(200);
        $response->assertSeeText('Bienvenido, Carlos Montoya');
        $response->assertSeeText('Hola, Carlos Montoya');
        $response->assertSeeText('Activar modo oscuro');
        $response->assertSeeText('Activar modo claro');
    }

    /** @test */
    public function test_home_shows_generic_welcome_when_authenticated_name_is_missing()
    {
        $user = ResearchStaffUser::create([
            'email' => 'noname@example.com',
            'password' => Hash::make('password'),
            'role' => 'research_staff',
            'state' => 1,
        ]);

        $response = $this->actingAs($user)->get(route('home'));

        $response->assertStatus(200);
        $response->assertSeeText('Bienvenido');
        $response->assertDontSeeText('Bienvenido,');
    }
}
