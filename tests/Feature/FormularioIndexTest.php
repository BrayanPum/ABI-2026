<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\ResearchStaff\ResearchStaffUser;
use Illuminate\Support\Facades\Hash;

class FormularioIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_formulario_index_renders()
    {
        $user = ResearchStaffUser::create([
            'email' => 'staff4@example.com',
            'password' => Hash::make('password'),
            'role' => 'research_staff',
            'state' => 1,
        ]);

        $response = $this->actingAs($user)->get(route('formulario.index'));
        $response->assertStatus(200);
        $response->assertSee('Módulo en construcción');
    }
}