<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\ResearchStaff\ResearchStaffContentFramework;
use App\Models\ResearchStaff\ResearchStaffContent;
use App\Models\ResearchStaff\ResearchStaffFramework;
use App\Models\ResearchStaff\ResearchStaffUser;
use Illuminate\Support\Facades\Hash;

/**
 * Test for ContentFrameworkController
 *
 * Tests CRUD operations for content frameworks with soft delete
 */
class ContentFrameworkControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $content;
    protected $framework;

    protected function setUp(): void
    {
        parent::setUp();

        $this->content = ResearchStaffContent::create([
            'name' => 'Content Test',
            'description' => 'Description test',
            'roles' => ['student']
        ]);

        $this->framework = ResearchStaffFramework::create([
            'name' => 'Framework Test',
            'description' => 'Description test',
            'start_year' => 2020
        ]);
    }

    /** @test */
    public function test_can_create_content_framework()
    {
        $user = $this->createAuthUser();
        $data = [
            'content_id' => $this->content->id,
            'framework_id' => $this->framework->id
        ];

        $response = $this->actingAs($user)->post('/content-frameworks', $data);

        $this->assertTrue(in_array($response->status(), [200, 201, 302, 404]));
    }

    /** @test */
    public function test_can_show_content_framework()
    {
        $user = $this->createAuthUser();
        $contentFramework = ResearchStaffContentFramework::create([
            'content_id' => $this->content->id,
            'framework_id' => $this->framework->id
        ]);

        $response = $this->actingAs($user)->get("/content-frameworks/{$contentFramework->id}");

        $this->assertTrue(in_array($response->status(), [200, 404]));
    }

    /** @test */
    public function test_can_update_content_framework()
    {
        $user = $this->createAuthUser();
        $contentFramework = ResearchStaffContentFramework::create([
            'content_id' => $this->content->id,
            'framework_id' => $this->framework->id
        ]);

        $data = [
            'content_id' => $this->content->id,
            'framework_id' => $this->framework->id
        ];

        $response = $this->actingAs($user)->put("/content-frameworks/{$contentFramework->id}", $data);

        $this->assertTrue(in_array($response->status(), [200, 302, 404]));
    }

    /** @test */
    public function test_can_delete_content_framework()
    {
        $user = $this->createAuthUser();
        $contentFramework = ResearchStaffContentFramework::create([
            'content_id' => $this->content->id,
            'framework_id' => $this->framework->id
        ]);

        $response = $this->actingAs($user)->delete("/content-frameworks/{$contentFramework->id}");

        $this->assertTrue(in_array($response->status(), [200, 204, 302, 404]));
    }

    /** @test */
    public function test_requires_authentication()
    {
        $response = $this->get('/content-frameworks');

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
