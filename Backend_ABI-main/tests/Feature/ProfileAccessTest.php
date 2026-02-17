<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\ResearchStaff\ResearchStaffUser;
use Illuminate\Support\Facades\Hash;

class ProfileAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_research_staff_can_access_edit()
    {
        $user = ResearchStaffUser::create([
            'email' => 'staff@example.com',
            'password' => Hash::make('password'),
            'role' => 'research_staff',
            'state' => 1,
        ]);

        $response = $this->actingAs($user)->get(route('perfil.edit'));
        $response->assertStatus(200);
        $response->assertViewIs('perfil');
    }

    public function test_non_staff_cannot_access_edit_and_can_view_show()
    {
        $student = ResearchStaffUser::create([
            'email' => 'student@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'state' => 1,
        ]);

        $edit = $this->actingAs($student)->get(route('perfil.edit'));
        $edit->assertStatus(302);

        $show = $this->actingAs($student)->get(route('perfil.show'));
        $show->assertStatus(200);
        $show->assertViewIs('perfil_show');
    }
}
