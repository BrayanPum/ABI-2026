<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthUserHelper
{
    public static function fullUser()
    {
        $user = Auth::user();

        if (! $user) {
            return null;
        }

        return User::with(['professor', 'student', 'researchstaff'])
            ->where('id', $user->id)
            ->first();
    }
}
