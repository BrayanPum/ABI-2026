<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // Check if the user is inactive
        $user = \App\Models\User::where('email', $request->email)->first();

        if ($user && $user->state == 0) {
            return back()->withErrors([
                'email' => 'Tu usuario está inactivo. Contacta al administrador.'
            ]);
        }

        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        try {
            if ($this->attemptLogin($request)) {
                return $this->sendLoginResponse($request);
            }

            $this->incrementLoginAttempts($request);

            return $this->sendFailedLoginResponse($request);
        } catch (\Exception $e) {
            Log::error('Database error: ' . $e->getMessage());

            return redirect()->back()->withErrors(['db_error' => 'Inténtelo más tarde.']);
            // dd($e->getMessage()); #ELIMINAR DESPUES DE PRUEBAS
        }
    }

    /**
     * Redirigir al usuario después de autenticarse.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return \Illuminate\Http\Response
     */
    protected function authenticated(Request $request, $user)
    {
        // Redirigir a la página de clientes si el usuario tiene el rol 'user'
        if ($user->role == 'user') {
            // return redirect()->route('clientes.index');
            return redirect()->route('home'); #ELIMINAR DESPUES DE PRUEBAS
        }

        // Redirigir a la página de inicio si no es un 'user'
        return redirect()->route('home');
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors([
                'login_error' => 'Usuario y/o contraseña incorrecta.',
            ]);
    }
}
