<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    // Показати форму реєстрації                                                                                                                                               
    public function showRegister()
    {
        return view('auth.register');
    }

    // Обробити реєстрацію                                                                                                                                                     
    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->email,  // копіюємо email замість name                                                                                                         
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    // Показати форму логіну                                                                                                                                                   
    public function showLogin()
    {
        return view('auth.login');
    }

    // Обробити логін                                                                                                                                                          
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'email' => 'Невірний email або пароль.',
        ]);
    }

    // Вихід                                                                                                                                                                   
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    // Dashboard                                                                                                                                                               
    public function dashboard()
    {
        return view('dashboard');
    }
}
