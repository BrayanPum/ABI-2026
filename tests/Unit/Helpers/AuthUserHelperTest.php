<?php

namespace Tests\Unit\Helpers;

use App\Helpers\AuthUserHelper;
use App\Models\Professor;
use App\Models\ResearchStaff;
use App\Models\Student;
use App\Models\User;
use Tests\TestCase;

class AuthUserHelperTest extends TestCase
{
    /** @test */
    public function it_builds_the_student_display_name_from_the_related_profile(): void
    {
        $user = new User(['role' => 'student']);
        $user->setRelation('student', new Student([
            'name' => 'Carlos',
            'last_name' => 'Montoya',
        ]));

        $this->assertSame('Carlos Montoya', AuthUserHelper::displayName($user));
    }

    /** @test */
    public function it_builds_the_professor_display_name_from_the_related_profile(): void
    {
        $user = new User(['role' => 'professor']);
        $user->setRelation('professor', new Professor([
            'name' => 'Ana',
            'last_name' => 'Rojas',
        ]));

        $this->assertSame('Ana Rojas', AuthUserHelper::displayName($user));
    }

    /** @test */
    public function it_builds_the_committee_leader_display_name_from_the_professor_profile(): void
    {
        $user = new User(['role' => 'committee_leader']);
        $user->setRelation('professor', new Professor([
            'name' => 'Laura',
            'last_name' => 'Gomez',
        ]));

        $this->assertSame('Laura Gomez', AuthUserHelper::displayName($user));
    }

    /** @test */
    public function it_builds_the_research_staff_display_name_from_the_related_profile(): void
    {
        $user = new User(['role' => 'research_staff']);
        $user->setRelation('researchstaff', new ResearchStaff([
            'name' => 'Miguel',
            'last_name' => 'Perez',
        ]));

        $this->assertSame('Miguel Perez', AuthUserHelper::displayName($user));
    }

    /** @test */
    public function it_returns_an_empty_string_when_the_user_has_no_name_information(): void
    {
        $user = new User(['role' => 'research_staff']);

        $this->assertSame('', AuthUserHelper::displayName($user));
    }
}
