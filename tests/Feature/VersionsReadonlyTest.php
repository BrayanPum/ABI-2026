<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\ResearchStaff\ResearchStaffUser;
use Illuminate\Support\Facades\Hash;

class VersionsReadonlyTest extends TestCase
{
    use RefreshDatabase;

    public function test_versions_index_is_readonly()
    {
        $user = ResearchStaffUser::create([
            'email' => 'staff2@example.com',
            'password' => Hash::make('password'),
            'role' => 'research_staff',
            'state' => 1,
        ]);

        $response = $this->actingAs($user)->get(route('versions.index'));
        $response->assertStatus(200);
        $response->assertSee('Listado de versiones');
        $response->assertDontSee('btn-outline-success');
        $response->assertDontSee('btn-outline-danger');
    }

    public function test_content_versions_index_is_readonly()
    {
        $user = ResearchStaffUser::create([
            'email' => 'staff3@example.com',
            'password' => Hash::make('password'),
            'role' => 'research_staff',
            'state' => 1,
        ]);

        $response = $this->actingAs($user)->get(route('content-versions.index'));
        $response->assertStatus(200);
        $response->assertSee('Registros diligenciados');
        $response->assertDontSee('btn-outline-success');
        $response->assertDontSee('btn-outline-danger');
    }
}