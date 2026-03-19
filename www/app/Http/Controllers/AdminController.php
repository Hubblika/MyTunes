<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
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
