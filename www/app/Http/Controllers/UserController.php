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
        $users = User::where('email', '!=', 'admin@example.com')->get();

        return response()->json($users);
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
}
