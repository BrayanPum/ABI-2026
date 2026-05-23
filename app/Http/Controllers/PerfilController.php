<?php

namespace App\Http\Controllers;

use App\Helpers\AuthUserHelper;
use App\Helpers\UserRoleHelper;
<<<<<<< Updated upstream
=======
use App\Models\User;
use App\Support\ProfileInputValidation;
use Illuminate\Http\RedirectResponse;
>>>>>>> Stashed changes
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PerfilController extends Controller
{
    public function show()
    {
        $user = AuthUserHelper::fullUser();
        $userMail = $user?->email ?? '';
        $userRole = $user?->role ?? '';
        $nameFromAccount = trim((string) ($user?->name ?? ''));
        $nameUserRole = UserRoleHelper::displayName($user);

        if ($userRole === 'student') {
            $userCard = $user?->student?->card_id;
            $userPhone = $user?->student?->phone;
            $userCity = $user?->student?->cityProgram?->city?->name;
            $userProgram = $user?->student?->cityProgram?->program?->name;
            $name = $user?->student?->name ?? $nameFromAccount;
            $surname = $user?->student?->last_name ?? '';
            $nameFromAccount = trim($name . ' ' . $surname);
        } elseif ($userRole === 'professor' || $userRole === 'committee_leader') {
            $userCard = $user?->professor?->card_id;
            $userPhone = $user?->professor?->phone;
            $userCity = $user?->professor?->cityProgram?->city?->name;
            $userProgram = $user?->professor?->cityProgram?->program?->name;
            $name = $user?->professor?->name ?? $nameFromAccount;
            $surname = $user?->professor?->last_name ?? '';
            $nameFromAccount = trim($name . ' ' . $surname);
        } else {
            $name = $user?->researchStaff?->name ?? $nameFromAccount;
            $userCard = $user?->researchStaff?->card_id;
            $userPhone = $user?->researchStaff?->phone;
            $userCity = 'N/A';
            $userProgram = 'N/A';
            $surname = $user?->researchStaff?->last_name ?? '';
            $nameFromAccount = trim($name . ' ' . $surname);
        }

        $displayName = $nameFromAccount !== '' ? $nameFromAccount : __('Usuario');

        return view('perfil_show', compact(
            'displayName',
            'userCity',
            'userProgram',
            'nameUserRole',
            'userMail',
            'userCard',
            'userPhone'
        ));
    }

    public function edit()
    {
        return view('perfil');
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

<<<<<<< Updated upstream
        $user->name = $request->input('name');
        $user->email = $request->input('email');
=======
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
            $rules['name'] = ProfileInputValidation::nameRules();
            $rules['last_name'] = ProfileInputValidation::nameRules();
            $rules['phone'] = ProfileInputValidation::phoneRules();
            $rules['email'] = [
                ...ProfileInputValidation::emailRules(),
                Rule::unique('users', 'email')->ignore($user->id),
            ];
        }

        if ($canManageProfileFields && $emailChanged) {
            $rules['email_confirmation'] = [
                'required',
                ...ProfileInputValidation::emailRules(),
                'same:email',
            ];
        }

        $validated = $request->validate($rules, array_merge(ProfileInputValidation::messages(), [
            'email_confirmation.required' => 'Debes confirmar el nuevo correo electronico.',
            'email_confirmation.same' => 'La confirmacion del correo debe coincidir.',
            'password.required' => 'Debes ingresar una nueva contrasena.',
            'password.min' => 'La contrasena debe tener al menos 9 caracteres.',
            'password.confirmed' => 'La confirmacion de la contrasena no coincide.',
        ]));

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
>>>>>>> Stashed changes

        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();

        return redirect()->route('perfil.edit')->with('status', 'Perfil actualizado correctamente');
    }
}
