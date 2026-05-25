<?php

namespace Tests\Unit\Views;

use App\Models\ResearchStaff;
use App\Models\User;
use Tests\TestCase;

class HeaderGreetingTest extends TestCase
{
    /** @test */
    public function it_renders_the_authenticated_user_name_in_the_header_greeting(): void
    {
        $user = new User(['role' => 'research_staff']);
        $user->setRelation('researchstaff', new ResearchStaff([
            'name' => 'Carlos',
            'last_name' => 'Montoya',
        ]));

        $this->be($user);

        $html = view('tablar::partials.header.sidebar-top', [
            'authenticatedUserDisplayName' => 'Carlos Montoya',
        ])->render();

        $this->assertStringContainsString('Bienvenido, Carlos Montoya', $html);
    }

    /** @test */
    public function it_renders_a_generic_header_greeting_when_the_name_is_missing(): void
    {
        $user = new User(['role' => 'research_staff']);
        $user->setRelation('researchstaff', new ResearchStaff());

        $this->be($user);

        $html = view('tablar::partials.header.sidebar-top', [
            'authenticatedUserDisplayName' => '',
        ])->render();

        $this->assertStringContainsString('Bienvenido', $html);
        $this->assertStringNotContainsString('Bienvenido,', $html);
    }

    /** @test */
    public function it_renders_theme_tooltips_in_spanish(): void
    {
        $html = view('tablar::partials.header.theme-mode')->render();

        $this->assertStringContainsString('Activar modo oscuro', $html);
        $this->assertStringContainsString('Activar modo claro', $html);
    }
}
