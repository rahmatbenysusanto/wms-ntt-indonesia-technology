<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Jenssegers\Agent\Agent;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function login(): View
    {
        $agent = new Agent();
        if ($agent->isDesktop()) {
            return view('auth.login');
        } else {
            return view('mobile.auth.login');
        }
    }

    public function loginPost(Request $request): \Illuminate\Http\RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        $user = User::where('username', $credentials['username'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            Auth::login($user);
            Session::put('user', $user);

            // User Has Menu
            $userHasMenu = DB::table('user_has_menu')
                ->leftJoin('menu', 'user_has_menu.menu_id', '=', 'menu.id')
                ->where('user_has_menu.user_id', $user->id)
                ->where('menu.type', 'web')
                ->pluck('menu.name');

            Session::put('userHasMenu', $userHasMenu);

            return redirect()->route('dashboard');
        }

        return back()->withErrors(['name' => 'Nama atau password salah'])->onlyInput('name');
    }

    public function logout(): \Illuminate\Http\RedirectResponse
    {
        Auth::logout();
        Session::invalidate();
        Session::regenerateToken();

        return redirect()->route('login');
    }

    // Function Mobile App

    public function loginPostMobile(Request $request): \Illuminate\Http\RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        $user = User::where('username', $credentials['username'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            Auth::login($user);
            Session::put('user', $user);

            // User Has Menu
            $userHasMenu = DB::table('user_has_menu')
                ->leftJoin('menu', 'user_has_menu.menu_id', '=', 'menu.id')
                ->where('user_has_menu.user_id', $user->id)
                ->where('menu.type', 'mobile')
                ->pluck('menu.name');

            Session::put('userHasMenu', $userHasMenu);

            return redirect()->route('dashboardMobile');
        }

        return back()->withErrors(['name' => 'Nama atau password salah'])->onlyInput('name');
    }
}
