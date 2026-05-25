<?php

namespace Tests\Unit\Views;

use App\Models\ResearchStaff;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class PerfilViewTest extends TestCase
{
    /** @test */
    public function research_staff_profile_hides_program_city_and_security_section(): void
    {
        view()->share('errors', new ViewErrorBag());

        $html = view('perfil_show', [
            'user' => new User(['role' => 'research_staff', 'state' => 1, 'email' => 'carlos@example.com']),
            'userRole' => 'research_staff',
            'displayName' => 'Carlos Montoya',
            'nameUserRole' => 'Personal de investigacion',
            'userMail' => 'carlos@example.com',
            'userCard' => '12345678',
            'userPhone' => '3001234567',
            'userProgram' => null,
            'userCity' => null,
            'showAcademicLocation' => false,
            'canEditProfile' => true,
            'canChangePassword' => true,
        ])->render();

        $this->assertStringContainsString('Editar perfil', $html);
        $this->assertStringContainsString('Subir nueva imagen', $html);
        $this->assertStringNotContainsString('Editar acceso', $html);
        $this->assertStringNotContainsString('Seguridad de la cuenta', $html);
        $this->assertStringNotContainsString('Administrar credenciales', $html);
        $this->assertStringNotContainsString('>Programa<', $html);
        $this->assertStringNotContainsString('>Ciudad<', $html);
    }

    /** @test */
    public function profile_view_renders_uploaded_photo_when_available(): void
    {
        view()->share('errors', new ViewErrorBag());
        Storage::fake('public');
        Storage::disk('public')->put('profile_photos/carlos.webp', 'avatar');

        $html = view('perfil_show', [
            'user' => new User([
                'role' => 'research_staff',
                'state' => 1,
                'email' => 'carlos@example.com',
                'profile_photo_path' => 'profile_photos/carlos.webp',
            ]),
            'userRole' => 'research_staff',
            'displayName' => 'Carlos Montoya',
            'nameUserRole' => 'Personal de investigacion',
            'userMail' => 'carlos@example.com',
            'userCard' => '12345678',
            'userPhone' => '3001234567',
            'userProgram' => null,
            'userCity' => null,
            'showAcademicLocation' => false,
            'canEditProfile' => true,
            'canChangePassword' => true,
        ])->render();

        $this->assertStringContainsString('/perfil/foto?path=profile_photos%2Fcarlos.webp', $html);
        $this->assertStringContainsString('abi-profile-avatar__image', $html);
    }

    /** @test */
    public function edit_profile_view_uses_real_profile_fields_instead_of_username(): void
    {
        view()->share('errors', new ViewErrorBag());

        $html = view('perfil', [
            'user' => new User(['role' => 'research_staff', 'email' => 'carlos@example.com']),
            'profile' => new ResearchStaff([
                'name' => 'Carlos',
                'last_name' => 'Montoya',
                'phone' => '3001234567',
            ]),
            'displayName' => 'Carlos Montoya',
            'userRole' => 'research_staff',
            'canManageProfileFields' => true,
        ])->render();

        $this->assertStringContainsString('Datos del perfil', $html);
        $this->assertStringContainsString('Nombre', $html);
        $this->assertStringContainsString('Apellido', $html);
        $this->assertStringContainsString('Correo electronico', $html);
        $this->assertStringContainsString('Telefono', $html);
        $this->assertStringContainsString('Confirmar nuevo correo', $html);
        $this->assertStringContainsString('Nueva contrasena', $html);
        $this->assertStringNotContainsString('Nombre de usuario', $html);
    }

    /** @test */
    public function profile_view_shows_current_profile_photo_update_entry_point(): void
    {
        view()->share('errors', new ViewErrorBag());
        Storage::fake('public');
        Storage::disk('public')->put('profile_photos/carlos.png', 'avatar');

        $html = view('perfil_show', [
            'user' => new User([
                'role' => 'research_staff',
                'state' => 1,
                'email' => 'carlos@example.com',
                'profile_photo_path' => 'profile_photos/carlos.png',
            ]),
            'displayName' => 'Carlos Montoya',
            'userRole' => 'research_staff',
            'nameUserRole' => 'Personal de investigacion',
            'userMail' => 'carlos@example.com',
            'userCard' => '12345678',
            'userPhone' => '3001234567',
            'userProgram' => null,
            'userCity' => null,
            'showAcademicLocation' => false,
            'canEditProfile' => true,
            'canChangePassword' => true,
        ])->render();

        $this->assertStringContainsString('/perfil/foto?path=profile_photos%2Fcarlos.png', $html);
        $this->assertStringContainsString('type="file"', $html);
        $this->assertStringContainsString('Subir nueva imagen', $html);
    }

    /** @test */
    public function non_staff_profile_shows_change_password_entry_point(): void
    {
        view()->share('errors', new ViewErrorBag());

        $html = view('perfil_show', [
            'user' => new User(['role' => 'student', 'state' => 1, 'email' => 'student@example.com']),
            'userRole' => 'student',
            'displayName' => 'Carlos Montoya',
            'nameUserRole' => 'Estudiante',
            'userMail' => 'student@example.com',
            'userCard' => '12345678',
            'userPhone' => '3001234567',
            'userProgram' => 'Ingenieria',
            'userCity' => 'Bucaramanga',
            'showAcademicLocation' => true,
            'canEditProfile' => false,
            'canChangePassword' => true,
        ])->render();

        $this->assertStringContainsString('Cambiar contrasena', $html);
        $this->assertStringNotContainsString('Editar perfil', $html);
    }

    /** @test */
    public function non_staff_edit_view_focuses_on_password_change(): void
    {
        view()->share('errors', new ViewErrorBag());

        $html = view('perfil', [
            'user' => new User(['role' => 'student', 'email' => 'student@example.com']),
            'profile' => new ResearchStaff([
                'name' => 'Carlos',
                'last_name' => 'Montoya',
                'phone' => '3001234567',
            ]),
            'displayName' => 'Carlos Montoya',
            'userRole' => 'student',
            'canManageProfileFields' => false,
        ])->render();

        $this->assertStringContainsString('Cambiar contrasena', $html);
        $this->assertStringContainsString('123456789', $html);
        $this->assertStringNotContainsString('Correo electronico', $html);
        $this->assertStringNotContainsString('Nombre de usuario', $html);
    }
}
