<?php

namespace Tests\Unit\Views;

use Tests\TestCase;

class HomeViewTest extends TestCase
{
    /** @test */
    public function it_renders_the_profile_photo_in_the_dashboard_card_when_available(): void
    {
        $html = view('home', [
            'displayName' => 'Carlos Montoya',
            'profilePhotoUrl' => '/perfil/foto?path=profile_photos%2Fcarlos.webp',
            'userTypeLabel' => 'Personal de investigacion',
            'userRole' => 'research_staff',
        ])->render();

        $this->assertStringContainsString('/perfil/foto?path=profile_photos%2Fcarlos.webp', $html);
        $this->assertStringContainsString('abi-home-avatar__image', $html);
    }

    /** @test */
    public function it_falls_back_to_the_udi_logo_when_the_user_has_no_profile_photo(): void
    {
        $html = view('home', [
            'displayName' => 'Carlos Montoya',
            'profilePhotoUrl' => null,
            'userTypeLabel' => 'Personal de investigacion',
            'userRole' => 'research_staff',
        ])->render();

        $this->assertStringContainsString('udi-logo.png', $html);
    }
}
