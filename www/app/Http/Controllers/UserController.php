<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return ok($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Creating users is handled by laravel
        return response()->noContent();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::whereUsername($id)->first();

        if (!$user) {
            return err(404);
        }

        return ok($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // TODO
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $key = $request->user()?->getKey();

        if (!$key) {
            return err(401);
        }

        $user = User::find($id);

        if (!$user) {
            return err(404);
        }

        if ($user->getKey() !== $key) {
            return err(403);
        }

        $user->delete();
        return response()->noContent();
    }

    public function updateAdminStatus(Request $request)
    {
        $request->validate([
            'users' => 'required|array',
            'users.*.id' => 'required|exists:users,id',
            'users.*.is_admin' => 'required|boolean',
        ]);

        foreach ($request->users as $userData) {
            $user = User::find($userData['id']);
            $user->is_admin = $userData['is_admin'];
            $user->save();
        }

        return response()->json(['message' => 'Admin status updated.']);
    }
}
