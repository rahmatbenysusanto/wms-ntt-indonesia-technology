<?php

namespace App\Http\Controllers;

use App\Models\User;
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
}
