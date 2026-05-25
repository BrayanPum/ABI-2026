<?php

namespace Tests\Unit\Controllers;

use App\Models\ResearchStaff\ResearchStaffResearchStaff;
use App\Models\ResearchStaff\ResearchStaffUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

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

        ResearchStaffResearchStaff::create([
            'user_id' => $user->id,
            'card_id' => '12345678',
            'name' => 'Carlos',
            'last_name' => 'Montoya',
            'phone' => '3001234567',
        ]);

        $response = $this->actingAs($user)->get(route('perfil.edit'));

        $response->assertStatus(200);
        $response->assertViewIs('perfil');
    }

    /** @test */
    public function test_can_update_profile()
    {
        $user = ResearchStaffUser::create([
            'email' => 'original@example.com',
            'password' => Hash::make('oldpassword'),
            'role' => 'research_staff',
            'state' => 1,
        ]);

        ResearchStaffResearchStaff::create([
            'user_id' => $user->id,
            'card_id' => '12345678',
            'name' => 'Original',
            'last_name' => 'Name',
            'phone' => '3001111111',
        ]);

        $data = [
            'name' => 'Carlos',
            'last_name' => 'Montoya',
            'email' => 'updated@example.com',
            'email_confirmation' => 'updated@example.com',
            'phone' => '3002222222',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ];

        $response = $this->actingAs($user)->put(route('perfil.update'), $data);

        $response->assertRedirect(route('perfil.show'));
        $response->assertSessionHas('status');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'updated@example.com',
        ]);
        $this->assertDatabaseHas('research_staff', [
            'user_id' => $user->id,
            'name' => 'Carlos',
            'last_name' => 'Montoya',
            'phone' => '3002222222',
        ]);

        $user->refresh();
        $this->assertTrue(Hash::check('newpassword', $user->password));
    }

    /** @test */
    public function test_validation_fails_with_invalid_email()
    {
        $user = ResearchStaffUser::create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role' => 'research_staff',
            'state' => 1,
        ]);

        ResearchStaffResearchStaff::create([
            'user_id' => $user->id,
            'card_id' => '12345678',
            'name' => 'Carlos',
            'last_name' => 'Montoya',
            'phone' => '3001111111',
        ]);

        $data = [
            'name' => 'Carlos',
            'last_name' => 'Montoya',
            'email' => 'invalid-email',
            'phone' => '3001111111',
        ];

        $response = $this->actingAs($user)->put(route('perfil.update'), $data);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function test_validation_requires_email_confirmation_only_when_email_changes()
    {
        $user = ResearchStaffUser::create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role' => 'research_staff',
            'state' => 1,
        ]);

        ResearchStaffResearchStaff::create([
            'user_id' => $user->id,
            'card_id' => '12345678',
            'name' => 'Carlos',
            'last_name' => 'Montoya',
            'phone' => '3001111111',
        ]);

        $data = [
            'name' => 'Carlos',
            'last_name' => 'Montoya',
            'email' => 'nuevo@example.com',
            'phone' => '3001111111',
        ];

        $response = $this->actingAs($user)->put(route('perfil.update'), $data);

        $response->assertSessionHasErrors('email_confirmation');
    }

    /** @test */
    public function test_validation_rejects_invalid_profile_field_formats(): void
    {
        $user = ResearchStaffUser::create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role' => 'research_staff',
            'state' => 1,
        ]);

        ResearchStaffResearchStaff::create([
            'user_id' => $user->id,
            'card_id' => '12345678',
            'name' => 'Carlos',
            'last_name' => 'Montoya',
            'phone' => '3001111111',
        ]);

        $response = $this->actingAs($user)->put(route('perfil.update'), [
            'name' => 'Carlos123',
            'last_name' => 'Montoya',
            'email' => 'correo-invalido',
            'phone' => '300-111',
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'phone']);

        $responseShortPhone = $this->actingAs($user)->put(route('perfil.update'), [
            'name' => 'Carlos',
            'last_name' => 'Montoya',
            'email' => 'test@example.com',
            'phone' => '300123',
        ]);

        $responseShortPhone->assertSessionHasErrors('phone');
    }

    /** @test */
    public function test_validation_fails_with_obvious_numeric_sequence_password()
    {
        $user = ResearchStaffUser::create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role' => 'research_staff',
            'state' => 1,
        ]);

        ResearchStaffResearchStaff::create([
            'user_id' => $user->id,
            'card_id' => '12345678',
            'name' => 'Carlos',
            'last_name' => 'Montoya',
            'phone' => '3001111111',
        ]);

        $data = [
            'name' => 'Carlos',
            'last_name' => 'Montoya',
            'email' => 'test@example.com',
            'phone' => '3001111111',
            'password' => '123456789',
            'password_confirmation' => '123456789',
        ];

        $response = $this->actingAs($user)->put(route('perfil.update'), $data);

        $response->assertSessionHasErrors('password');
    }

    /** @test */

    public function test_validation_fails_with_short_password()
    {
        $user = ResearchStaffUser::create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role' => 'research_staff',
            'state' => 1,
        ]);

        ResearchStaffResearchStaff::create([
            'user_id' => $user->id,
            'card_id' => '12345678',
            'name' => 'Carlos',
            'last_name' => 'Montoya',
            'phone' => '3001111111',
        ]);

        $data = [
            'name' => 'Carlos',
            'last_name' => 'Montoya',
            'email' => 'test@example.com',
            'phone' => '3001111111',
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
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role' => 'research_staff',
            'state' => 1,
        ]);

        ResearchStaffResearchStaff::create([
            'user_id' => $user->id,
            'card_id' => '12345678',
            'name' => 'Carlos',
            'last_name' => 'Montoya',
            'phone' => '3001111111',
        ]);

        $data = [
            'name' => 'Carlos',
            'last_name' => 'Montoya',
            'email' => 'test@example.com',
            'phone' => '3001111111',
            'password' => 'newpassword',
            'password_confirmation' => 'differentpassword',
        ];

        $response = $this->actingAs($user)->put(route('perfil.update'), $data);

        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function test_all_authenticated_users_can_access_password_change_and_view_show()
    {
        $student = ResearchStaffUser::create([
            'email' => 'student@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'state' => 1,
        ]);

        $editResponse = $this->actingAs($student)->get(route('perfil.edit'));
        $editResponse->assertStatus(200);
        $editResponse->assertViewIs('perfil');

        $showResponse = $this->actingAs($student)->get(route('perfil.show'));
        $showResponse->assertStatus(200);
        $showResponse->assertViewIs('perfil_show');
    }

    /** @test */
    public function test_authenticated_user_can_load_their_profile_photo_through_the_application_route()
    {
        Storage::fake('public');

        $user = ResearchStaffUser::create([
            'email' => 'photo@example.com',
            'password' => Hash::make('password'),
            'role' => 'research_staff',
            'state' => 1,
            'profile_photo_path' => 'profile_photos/carlos.jpg',
        ]);

        Storage::disk('public')->put('profile_photos/carlos.jpg', 'avatar-content');

        $response = $this->actingAs($user)->get(route('perfil.photo.show', [
            'path' => 'profile_photos/carlos.jpg',
        ]));

        $response->assertOk();
        $response->assertContent('avatar-content');
    }

    /** @test */
    public function test_authenticated_user_cannot_request_a_different_profile_photo_path()
    {
        Storage::fake('public');

        $user = ResearchStaffUser::create([
            'email' => 'photo@example.com',
            'password' => Hash::make('password'),
            'role' => 'research_staff',
            'state' => 1,
            'profile_photo_path' => 'profile_photos/carlos.jpg',
        ]);

        Storage::disk('public')->put('profile_photos/otra.jpg', 'avatar-content');

        $response = $this->actingAs($user)->get(route('perfil.photo.show', [
            'path' => 'profile_photos/otra.jpg',
        ]));

        $response->assertForbidden();
    }
}
