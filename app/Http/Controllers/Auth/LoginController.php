<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class LoginController extends Controller
{
    public function showLoginForm(): Response
    {
        return Inertia::render('Auth/Login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            if ($request->user()->is_active === false) {
                Auth::logout();

                return back()->withErrors([
                    'email' => 'This account is inactive. Contact your school administrator.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            if ($request->user()->hasRole('Parent')) {
                return redirect('/parent-portal');
            }

            if ($request->user()->hasRole('Teacher')) {
                return redirect('/teacher-portal');
            }

            if ($request->user()->hasRole('Accountant')) {
                return redirect('/accountant-portal');
            }

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
