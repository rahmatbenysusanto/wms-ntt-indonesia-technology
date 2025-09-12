<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\User;
use App\Models\UserHasMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::where('role', '!=', 'admin')->where('deleted_at', null)->paginate(10);

        $title = 'User';
        return view('user.index', compact('title', 'users'));
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            DB::beginTransaction();

            User::create([
                'username'  => $request->post('username'),
                'name'      => $request->post('name'),
                'email'     => $request->post('email'),
                'password'  => Hash::make($request->post('password')),
                'role'      => $request->post('role'),
            ]);

            DB::commit();
            return back()->with('success', 'User created successfully');
        } catch (\Exception $err) {
            DB::rollBack();
            Log::error($err->getMessage());
            Log::error($err->getTraceAsString());
            return back()->with('error', 'Create user failed');
        }
    }

    public function delete(Request $request): \Illuminate\Http\JsonResponse
    {
        User::where('id', $request->get('id'))
            ->update([
                'deleted_at' => now(),
            ]);

        return response()->json([
            'success' => true,
        ]);
    }

    public function find(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = User::find($request->get('id'));

        return response()->json([
            'success'   => true,
            'data'      => $user,
        ]);
    }

    public function update(Request $request): \Illuminate\Http\JsonResponse
    {
        User::where('id', $request->post('id'))
            ->update([
                'username'  => $request->post('username'),
                'name'      => $request->post('name'),
                'email'     => $request->post('email'),
            ]);

        if ($request->post('password') != "********") {
            User::where('id', $request->post('id'))
                ->update([
                    'password' => Hash::make($request->post('password')),
                ]);
        }

        return response()->json([
            'success'   => true,
        ]);
    }

    public function menu(Request $request): View
    {
        $user = User::find($request->query('id'));

        $userHasMenu = Menu::with(['userHasMenu' => function ($query) use ($user) {
            $query->where('user_id', $user->id);
        }])->get();

        $title = 'User';
        return view('user.menu', compact('title', 'userHasMenu', 'user'));
    }

    public function menuStore(Request $request): \Illuminate\Http\JsonResponse
    {
        if ($request->post('type') == 'disable') {
            UserHasMenu::where('menu_id', $request->post('menuId'))
                ->where('user_id', $request->post('userId'))
                ->delete();
        } else {
            UserHasMenu::create([
                'user_id' => $request->post('userId'),
                'menu_id' => $request->post('menuId'),
            ]);
        }

        return response()->json([
            'success' => true,
        ]);
    }
}
