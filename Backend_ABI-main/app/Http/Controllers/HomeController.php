<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Helpers\AuthUserHelper;

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

        if ($userRole == 'student') {
            $cp = $user?->student?->cityProgram;
            $name = $user?->student?->name ?? $nameFromAccount;
            $surname = $user?->student?->last_name ?? '';
            $nameFromAccount = trim($name . ' ' . $surname);
        } elseif ($userRole == 'professor' || $userRole == 'committee_leader') {
            $cp = $user?->professor?->cityProgram;
            $name = $user?->professor?->name ?? $nameFromAccount;
            $surname = $user?->professor?->last_name ?? '';
            $nameFromAccount = trim($name . ' ' . $surname);
        } else {
            $cp = null;
            $name = $user?->researchStaff?->name ?? $nameFromAccount;
            $surname = $user?->researchStaff?->last_name ?? '';
            $nameFromAccount = trim($name . ' ' . $surname);
        };

        $userProgram = $cp ? $cp->program->name . ' - ' . $cp->city->name : 'Personal de investigación';

        $displayName = $nameFromAccount !== '' ? $nameFromAccount : __('Usuario');
        return view('home', compact(
            'displayName',
            'userProgram',
            'userRole'
        ));
    }
}
