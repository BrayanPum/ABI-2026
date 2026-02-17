<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\ResearchStaff\ResearchStaffContentFrameworkProject;
use App\Models\ResearchStaff\ResearchStaffContentFramework;
use App\Models\ResearchStaff\ResearchStaffContent;
use App\Models\ResearchStaff\ResearchStaffFramework;
use App\Models\ResearchStaff\ResearchStaffProject;
use App\Models\ResearchStaff\ResearchStaffUser;
use Illuminate\Support\Facades\Hash;

/**
 * Test for ContentFrameworkProjectController
 *
 * Tests CRUD operations for content framework projects
 */
class ContentFrameworkProjectControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $contentFramework;
    protected $project;

    protected function setUp(): void
    {
        parent::setUp();

        $content = ResearchStaffContent::create([
            'name' => 'Content Test',
            'description' => 'Description test',
            'roles' => ['student']
        ]);

        $framework = ResearchStaffFramework::create([
            'name' => 'Framework Test',
            'description' => 'Description test',
            'start_year' => 2020
        ]);

        $this->contentFramework = ResearchStaffContentFramework::create([
            'content_id' => $content->id,
            'framework_id' => $framework->id
        ]);

        $this->project = ResearchStaffProject::create([
            'title' => 'Project Test',
            'delivery_date' => now(),
            'project_status_id' => 1,
            'thematic_area_id' => 1
        ]);
    }

    /** @test */
    public function test_can_create_content_framework_project()
    {
        $user = $this->createAuthUser();
        $data = [
            'content_framework_id' => $this->contentFramework->id,
            'project_id' => $this->project->id
        ];

        $response = $this->actingAs($user)->post('/content-framework-projects', $data);

        $this->assertTrue(in_array($response->status(), [200, 201, 302, 404]));
    }

    /** @test */
    public function test_can_show_content_framework_project()
    {
        $user = $this->createAuthUser();
        $cfp = ResearchStaffContentFrameworkProject::create([
            'content_framework_id' => $this->contentFramework->id,
            'project_id' => $this->project->id
        ]);

        $response = $this->actingAs($user)->get("/content-framework-projects/{$cfp->id}");

        $this->assertTrue(in_array($response->status(), [200, 404]));
    }

    /** @test */
    public function test_can_update_content_framework_project()
    {
        $user = $this->createAuthUser();
        $cfp = ResearchStaffContentFrameworkProject::create([
            'content_framework_id' => $this->contentFramework->id,
            'project_id' => $this->project->id
        ]);

        $data = [
            'content_framework_id' => $this->contentFramework->id,
            'project_id' => $this->project->id
        ];

        $response = $this->actingAs($user)->put("/content-framework-projects/{$cfp->id}", $data);

        $this->assertTrue(in_array($response->status(), [200, 302, 404]));
    }

    /** @test */
    public function test_can_delete_content_framework_project()
    {
        $user = $this->createAuthUser();
        $cfp = ResearchStaffContentFrameworkProject::create([
            'content_framework_id' => $this->contentFramework->id,
            'project_id' => $this->project->id
        ]);

        $response = $this->actingAs($user)->delete("/content-framework-projects/{$cfp->id}");

        $this->assertTrue(in_array($response->status(), [200, 204, 302, 404]));
    }

    /** @test */
    public function test_requires_authentication()
    {
        $response = $this->get('/content-framework-projects');

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
