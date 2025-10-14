<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class AuthLoginMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu.');
        }

        // User Has Menu
        $userHasMenu = DB::table('user_has_menu')
            ->leftJoin('menu', 'user_has_menu.menu_id', '=', 'menu.id')
            ->where('user_has_menu.user_id', Auth::id())
            ->where('menu.type', 'web')
            ->pluck('menu.name');

        Session::put('userHasMenu', $userHasMenu);

        return $next($request);
    }
}
