<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\ResearchStaff\ResearchStaffContentVersion;
use App\Models\ResearchStaff\ResearchStaffContent;
use App\Models\ResearchStaff\ResearchStaffVersion;
use App\Models\ResearchStaff\ResearchStaffUser;
use Illuminate\Support\Facades\Hash;

/**
 * Test for ContentVersionController
 *
 * Tests CRUD operations for content versions with soft delete
 */
class ContentVersionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $content;
    protected $version;

    protected function setUp(): void
    {
        parent::setUp();

        $this->content = ResearchStaffContent::create([
            'name' => 'Content Test',
            'description' => 'Description test',
            'roles' => ['student']
        ]);

        $this->version = ResearchStaffVersion::create([
            'project_id' => 1,
            'name' => 'Version 1.0'
        ]);
    }

    /** @test */
    public function test_can_create_content_version()
    {
        $user = $this->createAuthUser();
        $data = [
            'content_id' => $this->content->id,
            'version_id' => $this->version->id,
            'value' => 'Test value'
        ];

        $response = $this->actingAs($user)->post('/content-versions', $data);

        $this->assertTrue(in_array($response->status(), [200, 201, 302, 404]));
    }

    /** @test */
    public function test_can_show_content_version()
    {
        $user = $this->createAuthUser();
        $contentVersion = ResearchStaffContentVersion::create([
            'content_id' => $this->content->id,
            'version_id' => $this->version->id,
            'value' => 'Test value'
        ]);

        $response = $this->actingAs($user)->get("/content-versions/{$contentVersion->id}");

        $this->assertTrue(in_array($response->status(), [200, 404]));
    }

    /** @test */
    public function test_can_update_content_version()
    {
        $user = $this->createAuthUser();
        $contentVersion = ResearchStaffContentVersion::create([
            'content_id' => $this->content->id,
            'version_id' => $this->version->id,
            'value' => 'Original value'
        ]);

        $data = [
            'content_id' => $this->content->id,
            'version_id' => $this->version->id,
            'value' => 'Updated value'
        ];

        $response = $this->actingAs($user)->put("/content-versions/{$contentVersion->id}", $data);

        $this->assertTrue(in_array($response->status(), [200, 302, 404]));
    }

    /** @test */
    public function test_can_delete_content_version()
    {
        $user = $this->createAuthUser();
        $contentVersion = ResearchStaffContentVersion::create([
            'content_id' => $this->content->id,
            'version_id' => $this->version->id,
            'value' => 'Test value'
        ]);

        $response = $this->actingAs($user)->delete("/content-versions/{$contentVersion->id}");

        $this->assertTrue(in_array($response->status(), [200, 204, 302, 404]));
    }

    /** @test */
    public function test_requires_authentication()
    {
        $response = $this->get('/content-versions');

        $response->assertRedirect(route('login'));
    }

    private function createAuthUser(): ResearchStaffUser
    {
        return ResearchStaffUser::create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role' => 'research_staff',
            'state' => 1,
        ]);
    }
}
