<?php

namespace App\Http\Controllers;

use App\Helpers\AuthUserHelper;
use App\Helpers\UserRoleHelper;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PerfilController extends Controller
{
    public function show()
    {
        $user = AuthUserHelper::fullUser();
        $profile = $this->resolveProfile($user);
        $userRole = $user?->role ?? '';
        $displayName = AuthUserHelper::displayName($user);
        $nameUserRole = UserRoleHelper::displayName($user);
        $showAcademicLocation = $this->showsAcademicLocation($userRole);
        $canManageProfileFields = $this->canManageProfileFields($userRole);

        return view('perfil_show', [
            'user' => $user,
            'userRole' => $userRole,
            'displayName' => $displayName,
            'nameUserRole' => $nameUserRole,
            'userMail' => $user?->email ?? '',
            'userCard' => $profile?->card_id,
            'userPhone' => $profile?->phone,
            'userProgram' => $showAcademicLocation ? $this->resolveProgramName($user) : null,
            'userCity' => $showAcademicLocation ? $this->resolveCityName($user) : null,
            'showAcademicLocation' => $showAcademicLocation,
            'canEditProfile' => $canManageProfileFields,
            'canChangePassword' => $user instanceof User,
        ]);
    }

    public function showPhoto(Request $request)
    {
        $user = AuthUserHelper::fullUser();

        abort_unless($user instanceof User, 403);

        $requestedPath = ltrim(str_replace('\\', '/', trim((string) $request->query('path'))), '/');
        $currentPhotoPath = ltrim(str_replace('\\', '/', trim((string) ($user->profile_photo_path ?? ''))), '/');

        abort_if($requestedPath === '' || ! str_starts_with($requestedPath, 'profile_photos/'), 404);
        abort_unless($currentPhotoPath !== '' && hash_equals($currentPhotoPath, $requestedPath), 403);
        abort_unless(Storage::disk('public')->exists($requestedPath), 404);

        return Storage::disk('public')->response($requestedPath, basename($requestedPath), [
            'Cache-Control' => 'private, max-age=300',
        ]);
    }

    public function edit()
    {
        $user = AuthUserHelper::fullUser();
        $profile = $this->resolveProfile($user);
        $userRole = $user?->role ?? '';

        return view('perfil', [
            'user' => $user,
            'profile' => $profile,
            'displayName' => AuthUserHelper::displayName($user),
            'userRole' => $userRole,
            'canManageProfileFields' => $this->canManageProfileFields($userRole),
        ]);
    }

    public function updatePhoto(Request $request): RedirectResponse
    {
        $user = AuthUserHelper::fullUser();

        abort_unless($user instanceof User, 404);

        $validated = $request->validate([
            'profile_photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'profile_photo.required' => 'Debes seleccionar una imagen.',
            'profile_photo.image' => 'La foto de perfil debe ser una imagen valida.',
            'profile_photo.mimes' => 'La foto de perfil debe estar en formato JPG, JPEG, PNG o WEBP.',
            'profile_photo.max' => 'La foto de perfil no puede superar los 2 MB.',
        ]);

        $previousPhotoPath = trim((string) ($user->profile_photo_path ?? ''));
        $newPhotoPath = $validated['profile_photo']->store('profile_photos', 'public');

        $user->profile_photo_path = $newPhotoPath;
        $user->save();

        if ($previousPhotoPath !== '' && $previousPhotoPath !== $newPhotoPath) {
            Storage::disk('public')->delete($previousPhotoPath);
        }

        return redirect()
            ->route('perfil.show')
            ->with('status', 'Foto de perfil actualizada correctamente');
    }

    public function update(Request $request): RedirectResponse
    {
        $user = AuthUserHelper::fullUser();
        $profile = $this->resolveProfile($user);

        abort_unless($user instanceof User && $profile !== null, 404);

        $canManageProfileFields = $this->canManageProfileFields($user->role);
        $newEmail = trim((string) $request->input('email'));
        $currentEmail = trim((string) ($user->email ?? ''));
        $emailChanged = mb_strtolower($newEmail) !== mb_strtolower($currentEmail);

        $rules = [
            'password' => $this->passwordRules($canManageProfileFields ? 'nullable' : 'required'),
        ];

        if ($canManageProfileFields) {
            $rules['name'] = ['required', 'string', 'max:255'];
            $rules['last_name'] = ['required', 'string', 'max:255'];
            $rules['phone'] = ['required', 'string', 'max:20', 'regex:/^[0-9]+$/'];
            $rules['email'] = ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)];
        }

        if ($canManageProfileFields && $emailChanged) {
            $rules['email_confirmation'] = ['required', 'string', 'email', 'same:email'];
        }

        $validated = $request->validate($rules, [
            'email_confirmation.required' => 'Debes confirmar el nuevo correo electronico.',
            'email_confirmation.same' => 'La confirmacion del correo debe coincidir.',
            'password.required' => 'Debes ingresar una nueva contrasena.',
            'password.min' => 'La contrasena debe tener al menos 9 caracteres.',
            'password.confirmed' => 'La confirmacion de la contrasena no coincide.',
            'phone.regex' => 'El telefono solo debe contener numeros.',
        ]);

        if ($canManageProfileFields) {
            $profileUpdates = [
                'name' => trim((string) $validated['name']),
                'last_name' => trim((string) $validated['last_name']),
                'phone' => trim((string) $validated['phone']),
            ];

            foreach ($profileUpdates as $attribute => $value) {
                if ($profile->{$attribute} !== $value) {
                    $profile->{$attribute} = $value;
                }
            }

            if ($profile->isDirty()) {
                $profile->save();
            }

            if ($emailChanged) {
                $user->email = $newEmail;
            }
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        if ($user->isDirty()) {
            $user->save();
        }

        return redirect()
            ->route('perfil.show')
            ->with('status', 'Perfil actualizado correctamente');
    }

    private function canManageProfileFields(?string $role): bool
    {
        return (string) $role === 'research_staff';
    }

    private function passwordRules(string $presence): array
    {
        return [
            $presence,
            'string',
            'min:9',
            'confirmed',
            function (string $attribute, mixed $value, \Closure $fail): void {
                if (! is_string($value) || trim($value) === '') {
                    return;
                }

                if ($this->isObviousNumericSequence($value)) {
                    $fail('La contrasena no puede ser una secuencia numerica obvia como 123456789.');
                }
            },
        ];
    }

    private function isObviousNumericSequence(string $password): bool
    {
        if (! preg_match('/^\d{8,}$/', $password)) {
            return false;
        }

        $digits = str_split($password);
        $ascending = true;
        $descending = true;

        for ($index = 1, $length = count($digits); $index < $length; $index++) {
            $difference = (int) $digits[$index] - (int) $digits[$index - 1];
            $ascending = $ascending && $difference === 1;
            $descending = $descending && $difference === -1;
        }

        return $ascending || $descending;
    }

    private function resolveProfile(?User $user)
    {
        if (! $user instanceof User) {
            return null;
        }

        return match ($user->role) {
            'student' => $user->student,
            'professor', 'committee_leader' => $user->professor,
            default => $user->researchstaff,
        };
    }

    private function showsAcademicLocation(?string $role): bool
    {
        return in_array((string) $role, ['student', 'professor', 'committee_leader'], true);
    }

    private function resolveProgramName(?User $user): ?string
    {
        return match ($user?->role) {
            'student' => $user?->student?->cityProgram?->program?->name,
            'professor', 'committee_leader' => $user?->professor?->cityProgram?->program?->name,
            default => null,
        };
    }

    private function resolveCityName(?User $user): ?string
    {
        return match ($user?->role) {
            'student' => $user?->student?->cityProgram?->city?->name,
            'professor', 'committee_leader' => $user?->professor?->cityProgram?->city?->name,
            default => null,
        };
    }
}
