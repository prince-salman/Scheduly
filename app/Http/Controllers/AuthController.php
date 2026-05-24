<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Show the registration form.
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Handle a new registration submission.
     */
    public function storeRegister(Request $request)
    {
        // TODO: validate input, create user with status='pending', send notification to admin
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        return redirect()->route('pending');
    }

    /**
     * Show the login form.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Attempt to log the user in.
     */
    public function login(Request $request)
    {
        // TODO: validate credentials, check user status before allowing in,
        //       redirect to 'pending' or 'rejected' if applicable
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        return redirect()->route('dashboard');
    }

    /**
     * Log the user out and kill the session.
     */
    public function logout(Request $request)
    {
        // TODO: call Auth::logout(), invalidate session
        return redirect()->route('login');
    }

    /**
     * Show the "account pending approval" screen.
     */
    public function pending()
    {
        return view('auth.pending');
    }

    /**
     * Show the "account rejected" screen with optional reason.
     */
    public function rejected()
    {
        return view('auth.rejected');
    }
}
