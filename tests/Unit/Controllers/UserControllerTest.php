<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\ResearchStaff\ResearchStaffUser;
use App\Models\ResearchStaff\ResearchStaffStudent;
use App\Models\ResearchStaff\ResearchStaffProfessor;
use App\Models\ResearchStaff\ResearchStaffResearchStaff;
use App\Models\ResearchStaff\ResearchStaffCityProgram;
use App\Models\ResearchStaff\ResearchStaffCity;
use App\Models\ResearchStaff\ResearchStaffProgram;
use Illuminate\Support\Facades\Hash;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $cityProgram;

    protected function setUp(): void
    {
        parent::setUp();

        // Create necessary relations
        $city = ResearchStaffCity::create(['name' => 'Test City', 'department_id' => 1]);
        $program = ResearchStaffProgram::create([
            'name' => 'Test Program',
            'research_group_id' => 1
        ]);
        $this->cityProgram = ResearchStaffCityProgram::create([
            'city_id' => $city->id,
            'program_id' => $program->id
        ]);
    }

    // ========================
    // INDEX TESTS
    // ========================

    /** @test */
    public function test_can_list_users()
    {
        $user = $this->createUserWithRole('research_staff');

        $response = $this->actingAs($user)->get(route('users.index'));

        $response->assertStatus(200);
        $response->assertViewIs('users.index');
        $response->assertViewHas('users');
    }

    /** @test */
    public function test_can_search_users()
    {
        $user = $this->createUserWithRole('research_staff');
        $testUser = $this->createUserWithRole('student', 'test@example.com');

        $response = $this->actingAs($user)->get(route('users.index', ['search' => 'test']));

        $response->assertStatus(200);
        $response->assertViewHas('users');
    }

    /** @test */
    public function test_can_filter_by_role()
    {
        $user = $this->createUserWithRole('research_staff');

        $response = $this->actingAs($user)->get(route('users.index', ['role' => 'student']));

        $response->assertStatus(200);
        $response->assertViewHas('role', 'student');
    }

    /** @test */
    public function test_can_filter_by_state()
    {
        $user = $this->createUserWithRole('research_staff');

        $response = $this->actingAs($user)->get(route('users.index', ['state' => 1]));

        $response->assertStatus(200);
        $response->assertViewHas('state', 1);
    }

    /** @test */
    public function test_pagination_works_correctly()
    {
        $user = $this->createUserWithRole('research_staff');

        $response = $this->actingAs($user)->get(route('users.index', ['per_page' => 10]));

        $response->assertStatus(200);
        $response->assertViewHas('perPage', 10);
    }

    // ========================
    // SHOW TESTS
    // ========================

    /** @test */
    public function test_can_show_student_user()
    {
        $authUser = $this->createUserWithRole('research_staff');
        $student = $this->createUserWithRole('student');

        $response = $this->actingAs($authUser)->get(route('users.show', $student));

        $response->assertStatus(200);
        $response->assertViewIs('users.show');
        $response->assertViewHas('user');
        $response->assertViewHas('details');
    }

    /** @test */
    public function test_can_show_professor_user()
    {
        $authUser = $this->createUserWithRole('research_staff');
        $professor = $this->createUserWithRole('professor');

        $response = $this->actingAs($authUser)->get(route('users.show', $professor));

        $response->assertStatus(200);
        $response->assertViewHas('user');
        $response->assertViewHas('details');
    }

    /** @test */
    public function test_returns_404_for_nonexistent_user()
    {
        $user = $this->createUserWithRole('research_staff');

        $response = $this->actingAs($user)->get(route('users.show', 99999));

        $response->assertStatus(404);
    }

    // ========================
    // UPDATE TESTS
    // ========================

    /** @test */
    public function test_can_update_user_basic_info()
    {
        $authUser = $this->createUserWithRole('research_staff');
        $student = $this->createUserWithRole('student');

        $data = [
            'email' => 'updated@example.com',
            'state' => 1,
            'role' => 'student',
            'card_id' => '123456789',
            'name' => 'Updated Name',
            'last_name' => 'Updated Last Name',
            'phone' => '1234567890',
            'semester' => 5,
            'city_program_id' => $this->cityProgram->id,
        ];

        $response = $this->actingAs($authUser)->put(route('users.update', $student), $data);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'id' => $student->id,
            'email' => 'updated@example.com'
        ]);
    }

    /** @test */
    public function test_can_update_user_password()
    {
        $authUser = $this->createUserWithRole('research_staff');
        $student = $this->createUserWithRole('student');

        $data = [
            'email' => $student->email,
            'state' => 1,
            'role' => 'student',
            'card_id' => '123456789',
            'name' => 'Test Name',
            'last_name' => 'Test Last Name',
            'phone' => '1234567890',
            'semester' => 5,
            'city_program_id' => $this->cityProgram->id,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ];

        $response = $this->actingAs($authUser)->put(route('users.update', $student), $data);

        $response->assertRedirect(route('users.index'));
        $student->refresh();
        $this->assertTrue(Hash::check('newpassword123', $student->password));
    }

    /** @test */
    public function test_can_change_user_role()
    {
        $authUser = $this->createUserWithRole('research_staff');
        $student = $this->createUserWithRole('student');

        $data = [
            'email' => $student->email,
            'state' => 1,
            'role' => 'professor',
            'card_id' => '987654321',
            'name' => 'Test Name',
            'last_name' => 'Test Last Name',
            'phone' => '1234567890',
            'city_program_id' => $this->cityProgram->id,
        ];

        $response = $this->actingAs($authUser)->put(route('users.update', $student), $data);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'id' => $student->id,
            'role' => 'professor'
        ]);
    }

    /** @test */
    public function test_validation_fails_with_invalid_email()
    {
        $authUser = $this->createUserWithRole('research_staff');
        $student = $this->createUserWithRole('student');

        $data = [
            'email' => 'invalid-email',
            'state' => 1,
            'role' => 'student',
        ];

        $response = $this->actingAs($authUser)->put(route('users.update', $student), $data);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function test_validation_fails_with_duplicate_email()
    {
        $authUser = $this->createUserWithRole('research_staff');
        $student1 = $this->createUserWithRole('student', 'student1@example.com');
        $student2 = $this->createUserWithRole('student', 'student2@example.com');

        $data = [
            'email' => 'student1@example.com',
            'state' => 1,
            'role' => 'student',
            'card_id' => '123456789',
            'name' => 'Test',
            'last_name' => 'Test',
            'phone' => '1234567890',
            'semester' => 5,
            'city_program_id' => $this->cityProgram->id,
        ];

        $response = $this->actingAs($authUser)->put(route('users.update', $student2), $data);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function test_validation_fails_with_missing_required_fields()
    {
        $authUser = $this->createUserWithRole('research_staff');
        $student = $this->createUserWithRole('student');

        $data = [
            'email' => '',
            'role' => 'student',
        ];

        $response = $this->actingAs($authUser)->put(route('users.update', $student), $data);

        $response->assertSessionHasErrors(['email', 'state']);
    }

    // ========================
    // DEACTIVATE/ACTIVATE TESTS
    // ========================

    /** @test */
    public function test_can_deactivate_user()
    {
        $authUser = $this->createUserWithRole('research_staff');
        $student = $this->createUserWithRole('student');

        $response = $this->actingAs($authUser)->delete(route('users.destroy', $student));

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'id' => $student->id,
            'state' => 0
        ]);
    }

    /** @test */
    public function test_can_activate_user()
    {
        $authUser = $this->createUserWithRole('research_staff');
        $student = $this->createUserWithRole('student');
        $student->state = 0;
        $student->save();

        $response = $this->actingAs($authUser)->post(route('users.activate', $student));

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'id' => $student->id,
            'state' => 1
        ]);
    }

    /** @test */
    public function test_requires_authentication()
    {
        $student = $this->createUserWithRole('student');

        $response = $this->get(route('users.index'));

        $response->assertRedirect(route('login'));
    }

    // ========================
    // HELPER METHODS
    // ========================

    private function createUserWithRole(string $role, string $email = null): ResearchStaffUser
    {
        $user = ResearchStaffUser::create([
            'email' => $email ?? $role . '@example.com',
            'password' => Hash::make('password'),
            'role' => $role,
            'state' => 1,
        ]);

        switch ($role) {
            case 'student':
                ResearchStaffStudent::create([
                    'user_id' => $user->id,
                    'card_id' => 'CARD' . $user->id,
                    'name' => 'Test',
                    'last_name' => 'Student',
                    'phone' => '1234567890',
                    'semester' => 1,
                    'city_program_id' => $this->cityProgram->id,
                ]);
                break;
            case 'professor':
            case 'committee_leader':
                ResearchStaffProfessor::create([
                    'user_id' => $user->id,
                    'card_id' => 'CARD' . $user->id,
                    'name' => 'Test',
                    'last_name' => 'Professor',
                    'phone' => '1234567890',
                    'committee_leader' => $role === 'committee_leader' ? 1 : 0,
                    'city_program_id' => $this->cityProgram->id,
                ]);
                break;
            case 'research_staff':
                ResearchStaffResearchStaff::create([
                    'user_id' => $user->id,
                    'card_id' => 'CARD' . $user->id,
                    'name' => 'Test',
                    'last_name' => 'Staff',
                    'phone' => '1234567890',
                ]);
                break;
        }

        return $user;
    }
}
