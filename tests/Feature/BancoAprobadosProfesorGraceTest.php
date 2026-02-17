<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\ResearchStaff\ResearchStaffUser;
use App\Models\ResearchStaff\ResearchStaffProfessor;
use Illuminate\Support\Facades\Hash;

class BancoAprobadosProfesorGraceTest extends TestCase
{
    use RefreshDatabase;

    public function test_professor_without_program_sees_empty_with_message()
    {
        $user = ResearchStaffUser::create([
            'email' => 'noprog@example.com',
            'password' => Hash::make('password'),
            'role' => 'professor',
            'state' => 1,
        ]);

        \DB::connection('mysql_research_staff')->table('professors')->insert([
            'user_id' => $user->id,
            'card_id' => 'DOC002',
            'name' => 'No',
            'last_name' => 'Program',
            'mail' => 'noprog@example.com',
            'phone' => '123',
            'state' => 1,
            'committee_leader' => 0,
            'city_program_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('professor.projects.approved.index'));
        $response->assertStatus(200);
        $response->assertSee('Completa tu asignación de programa');
    }
}
