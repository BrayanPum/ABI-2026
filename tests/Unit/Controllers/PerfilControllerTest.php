<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\ResearchStaff\ResearchStaffUser;
use Illuminate\Support\Facades\Hash;

class PerfilControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_research_staff_can_view_edit_profile_page()
    {
        $user = ResearchStaffUser::create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role' => 'research_staff',
            'state' => 1,
        ]);

        $response = $this->actingAs($user)->get(route('perfil.edit'));

        $response->assertStatus(200);
        $response->assertViewIs('perfil');
    }

    /** @test */
    public function test_can_update_profile()
    {
        $user = ResearchStaffUser::create([
            'name' => 'Original Name',
            'email' => 'original@example.com',
            'password' => Hash::make('oldpassword'),
            'role' => 'student',
            'state' => 1,
        ]);

        $data = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ];

        $response = $this->actingAs($user)->put(route('perfil.update'), $data);

        $response->assertRedirect(route('perfil.edit'));
        $response->assertSessionHas('status');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com'
        ]);

        $user->refresh();
        $this->assertTrue(Hash::check('newpassword', $user->password));
    }

    /** @test */
    public function test_validation_fails_with_invalid_email()
    {
        $user = ResearchStaffUser::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'state' => 1,
        ]);

        $data = [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ];

        $response = $this->actingAs($user)->put(route('perfil.update'), $data);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function test_validation_fails_with_short_password()
    {
        $user = ResearchStaffUser::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'state' => 1,
        ]);

        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => '123',
            'password_confirmation' => '123',
        ];

        $response = $this->actingAs($user)->put(route('perfil.update'), $data);

        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function test_validation_fails_with_mismatched_password()
    {
        $user = ResearchStaffUser::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'state' => 1,
        ]);

        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'newpassword',
            'password_confirmation' => 'differentpassword',
        ];

        $response = $this->actingAs($user)->put(route('perfil.update'), $data);

        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function test_requires_role_for_edit_and_all_can_view_show()
    {
        $student = ResearchStaffUser::create([
            'email' => 'student@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'state' => 1,
        ]);

        $editResponse = $this->actingAs($student)->get(route('perfil.edit'));
        $editResponse->assertStatus(403);

        $showResponse = $this->actingAs($student)->get(route('perfil.show'));
        $showResponse->assertStatus(200);
        $showResponse->assertViewIs('perfil_show');
    }
}
