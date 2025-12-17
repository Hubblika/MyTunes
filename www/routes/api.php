<?php

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

require __DIR__.'/utils.php';


// Route::middleware(['web'])->group(fn () => [
//     Route::post('/account/register', function (Request $request) {
//         if (!$request->isJson()) {
//             return err(400, ApiError::INVALID_CONTENT_TYPE);
//         }

//         $email = $request->json('email');
//         $password = $request->json('password');

//         if (is_valid_email($email)) {
//             return err(400, ApiError::INVALID_EMAIL);
//         }

//         if (is_valid_password($password)) {
//             return err(400, ApiError::INVALID_PASSWORD);
//         }

//         $user_id = User::create([
//             'username' => generate_username(),
//             'email' => $email,
//             'password' => $password
//         ])->getKey();

//         $user = User::find($user_id);
//         Auth::login($user, true);

//         return redirect('/');
//     }),

//     Route::post('/account/login', function (Request $request): JsonResponse {
//         if (!$request->isJson()) {
//             return err(400, ApiError::INVALID_CONTENT_TYPE);
//         }

//         $email = $request->json('email');
//         $password = $request->json('password');

//         if (is_valid_email($email)) {
//             return err(400, ApiError::INVALID_EMAIL);
//         }

//         if (is_valid_password($password)) {
//             return err(400, ApiError::INVALID_PASSWORD);
//         }

//         $user = User::whereEmail($email)->first();

//         if ($user === null) {
//             return err(404, ApiError::USER_NOT_FOUND);
//         }

//         if (!Hash::check($password, $user->password)) {
//             return err(401, ApiError::INCORRECT_PASSWORD);
//         }

//         Auth::login($user, true);

//         return ok('done');
//     })
// ]);



// Route::get('/user', function (Request $request): JsonResponse {
//     return ok($request->user());
// })->middleware('auth:sanctum');
