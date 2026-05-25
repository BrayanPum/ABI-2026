<?php

namespace App\Http\Controllers;

use App\Helpers\AuthUserHelper;
use App\Helpers\UserRoleHelper;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = AuthUserHelper::fullUser();
        $userRole = $user?->role ?? '';
        $displayName = AuthUserHelper::displayName($user);
        $profilePhotoUrl = $user?->profile_photo_url;
        $userTypeLabel = UserRoleHelper::displayName($user);

        return view('home', compact(
            'displayName',
            'profilePhotoUrl',
            'userTypeLabel',
            'userRole'
        ));
    }
}
