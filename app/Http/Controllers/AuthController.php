<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    // Показати форму реєстрації 登録フォームを表示                                                                                                                                      
    public function showRegister()
    {
        return view('auth.register');
    }

    // Обробити реєстрацію 登録を処理                                                                                                                                             
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,                                                                                                          
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    // Показати форму логіну ログインフォームを表示                                                                                                                                                  
    public function showLogin()
    {
        return view('auth.login');
    }

    // Обробити логін  ログインを処理                                                                                                                                                     
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
            'email' => 'Invalid email or password.',
        ]);
    }

    // Вихід    ログアウト                                                                                                                                                               
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    // Dashboard   ダッシュボード                                                                                                                                                            
    public function dashboard()
    {
        return view('dashboard');
    }

    public function profile()
    {
        return view('auth.profile');
    }
    public function updateProfile(Request $request)
    {
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,',
            'password' => 'nullable|min:6|confirmed',
        ]);
        $user = User::find(Auth::id());
        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('profile')->with('success', 'プロフィールが更新されました。');
    }
}
