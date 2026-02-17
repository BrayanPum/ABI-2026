<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\ResearchStaff\ResearchStaffUser;
use App\Models\ResearchStaff\ResearchStaffProfessor;
use App\Models\ResearchStaff\ResearchStaffCityProgram;
use Illuminate\Support\Facades\Hash;

class ParticipantsPageTest extends TestCase
{
    use RefreshDatabase;

    protected function makeProfessorUser(): ResearchStaffUser
    {
        $program = ResearchStaffCityProgram::create([
            'city_id' => 1,
            'program_id' => 1,
        ]);

        $user = ResearchStaffUser::create([
            'email' => 'prof@example.com',
            'password' => Hash::make('password'),
            'role' => 'professor',
            'state' => 1,
        ]);

        ResearchStaffProfessor::create([
            'user_id' => $user->id,
            'card_id' => 'DOC001',
            'name' => 'Doc',
            'last_name' => 'Tor',
            'phone' => '123',
            'committee_leader' => 0,
            'city_program_id' => $program->id,
        ]);

        return $user;
    }

    public function test_professor_can_open_participants_page()
    {
        $user = $this->makeProfessorUser();
        $response = $this->actingAs($user)->get(route('participants.index'));
        $response->assertStatus(200);
        $response->assertViewIs('participants.index');
    }

    public function test_student_cannot_open_participants_page()
    {
        $student = ResearchStaffUser::create([
            'email' => 'student2@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'state' => 1,
        ]);

        $response = $this->actingAs($student)->get(route('participants.index'));
        $response->assertStatus(302);
    }
}
