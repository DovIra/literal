<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Enums\UserType;
use App\Models\Event;
use Carbon\Carbon;
use App\Models\Notification;
use App\Enums\Type;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:10'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', 'max:16', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'user_type' => UserType::Regular,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'created_by' => 0,
            'updated_by' => 0,
        ]);

        // 開催日が今日以降のイベントを取得
        $upcomingEvents = Event::where('event_date', '>=', Carbon::today())->get();

        // 通知を作成（type: new event）
        foreach ($upcomingEvents as $event) {
            Notification::firstOrCreate([
                'user_id' => $user->id,
                'event_id' => $event->id,
                'type' => Type::NewEvent->value,
            ], [
                'created_by' => 0,
                'updated_by' => 0,
            ]);
        }

        event(new Registered($user));
        return redirect() -> route('login');
    }
}
