<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

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
        $this->middleware('auth')->only('logout');
    }
    protected function authenticated(Request $request, $user)
{
    // Verificamos si el rol es 1 y registramos el rol del usuario

    if ($user->rol == 1) {
        // Si el rol es 1, redirigimos al panel de control
        return redirect()->route('panel_control');
    }

    // Si el rol no es 1, redirigimos al inicio
    return redirect('inicio');
}

public function login(Request $request)
{
    $credentials = $request->only('email', 'password');

    // Registramos las credenciales recibidas para verificar

    if (Auth::attempt($credentials)) {
        // Si las credenciales son correctas, redirigir según el rol del usuario
        return $this->authenticated($request, Auth::user());
    }

    // Si las credenciales no son correctas, registramos el fallo
    
    // Redirigir de vuelta con un mensaje de error
    return redirect()->back()->withErrors([
        'email' => 'El usuario o la contraseña son incorrectos.',
    ])->withInput($request->except('password'));
}
}
