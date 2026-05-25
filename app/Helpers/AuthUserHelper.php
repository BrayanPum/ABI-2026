<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthUserHelper
{
    public static function fullUser(): ?User
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            return null;
        }

        $relation = self::relationForRole($user->role);

        if ($relation !== null) {
            $user->loadMissing($relation);
        }

        return $user;
    }

    public static function displayName(?User $user = null): string
    {
        $user = $user ?? self::fullUser();

        if (! $user instanceof User) {
            return '';
        }

        $nameFromAccount = trim((string) ($user->name ?? ''));

        return match ($user->role) {
            'student' => self::composeName(
                $user->student?->name ?? $nameFromAccount,
                $user->student?->last_name ?? ''
            ),
            'professor', 'committee_leader' => self::composeName(
                $user->professor?->name ?? $nameFromAccount,
                $user->professor?->last_name ?? ''
            ),
            default => self::composeName(
                $user->researchstaff?->name ?? $nameFromAccount,
                $user->researchstaff?->last_name ?? ''
            ),
        };
    }

    protected static function relationForRole(?string $role): ?string
    {
        return match ($role) {
            'student' => 'student',
            'professor', 'committee_leader' => 'professor',
            default => 'researchstaff',
        };
    }

    protected static function composeName(?string $name, ?string $lastName): string
    {
        return trim(implode(' ', array_filter([
            trim((string) $name),
            trim((string) $lastName),
        ])));
    }
}
