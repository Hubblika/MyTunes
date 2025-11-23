<?php

use App\Http\Middleware\RequiresJson;
use App\Http\Middleware\RequiresLogin;
use App\Models\Session;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as Status;
use Illuminate\Support\Facades\Route;

/**
 * Enum containing common http errors to be handled by the Vue frontend
 */
enum HttpError {
    case UNKNOWN;

    // 400 Bad Request
    case INVALID_CONTENT_TYPE;
    case INVALID_EMAIL;
    case INVALID_PASSWORD;

    // 401 Unauthorized
    case UNAUTHORIZED;
    case INCORRECT_PASSWORD;

    // 404 Not Found
    case USER_NOT_FOUND;
}

/**
 * Name of the cookie storing the session token
 * @var string
 */
const AUTH_TOKEN = 'mytunes_auth_token';

/**
 * Shorthand for hashing data using SHA256 and returning the binary result
 * @param string $data
 * @return string
 */
function sha256(string $data) {
    return hash('sha256', $data, true);
}

/**
 * Gets the current session being used to make the request, or `null` if `AUTH_TOKEN` isn't in the `cookie` header
 * @param Request $request
 * @return User
 */
function current_session(Request $request): ?Session {
    $token = $request->cookie(AUTH_TOKEN);

    if ($token === null) {
        return null;
    }

    $decoded = base64_decode($token, true);

    if (!$decoded) {
        return null;
    }

    return Session::find(sha256($decoded));
}



/**
 * Shorthand for returning json data to the client
 * @param int $status Status code
 * @param mixed $data Deserializable data
 * @return JsonResponse The json response
 */
function ok(mixed $data, int $status = 200): JsonResponse {
    return response()->json(['data' => $data], $status);
}

/**
 * Shorthand for returning a json error response to the client
 * @param int $status Status code of the error
 * @param HttpError $error The HTTP error kind which will determine what data is displayed
 * @return JsonResponse The json response
 */
function err(int $status, HttpError $error = HttpError::UNKNOWN, ?string $message = null): JsonResponse {
    return response()->json(
        [
            'error' => [
                'status' => $status,
                'name' => $error->name,
                'message' => $message
            ]
        ],
        $status
    );
}



/**
 * Inserts a new `Session` into the database and returns the auth cookie value
 * @param User $user
 * @param ?Request $request
 * @return Symfony\Component\HttpFoundation\Cookie The base64 encoded cookie
 */
function new_session(User $user, ?Request $request) {
    $bytes = random_bytes(64);
    $token = sha256($bytes);
    Session::create([
        'token_hash' => $token,
        'created_at' => Date::now(),
        'ip_address' => $request?->ip(),
        'user_agent' => $request?->userAgent(),
        'user_id' => $user->getKey()
    ]);

    return cookie(
        name: AUTH_TOKEN,
        value: base64_encode($bytes),
        minutes: 60 * 24 * 93,
        path: '/',
        secure: true,
        httpOnly: true
    );
}



Route::prefix('/account')->middleware([RequiresJson::class])->group(fn () => [
    // Route to register a new account
    // Creates a `User` and `Session`, and sets the auth cookie
    Route::match(['post', 'put'], '/register', function (Request $request) {
        $email = $request->json('email');
        $password = $request->json('password');

        if ($email === null || preg_match('/^[a-z-.]+@([a-z-]+.)+[a-z-]{2,6}$/i', $email)) {
            return err(Status::HTTP_BAD_REQUEST, HttpError::INVALID_EMAIL);
        }

        if ($password === null || strlen($password) < 8) {
            return err(Status::HTTP_BAD_REQUEST, HttpError::INVALID_PASSWORD);
        }

        $user = User::create([
            'email' => $email,
            'password_hash' => sha256($password)
        ]);

        $cookie = new_session($user, $request);

        return ok($user, Status::HTTP_CREATED)->withCookie($cookie);
    }),

    // Route to log into an existing user
    // Creates a `Session` and sets the auth cookie
    Route::middleware([RequiresJson::class])->post('/login', function (Request $request) {
        $email = $request->json('email');
        $password = $request->json('password');

        if ($email === null || preg_match('/^[a-z-.]+@([a-z-]+.)+[a-z-]{2,6}$/i', $email)) {
            return err(Status::HTTP_BAD_REQUEST, HttpError::INVALID_EMAIL);
        }

        if ($password === null || strlen($password) < 8) {
            return err(Status::HTTP_BAD_REQUEST, HttpError::INVALID_PASSWORD);
        }

        $user = User::where('email', $email)->first();

        if ($user === null) {
            return err(Status::HTTP_NOT_FOUND, HttpError::USER_NOT_FOUND);
        }

        if ($user->getAttribute('password_hash') !== sha256($password)) {
            return err(Status::HTTP_UNAUTHORIZED, HttpError::INCORRECT_PASSWORD);
        }

        $cookie = new_session($user, $request);

        return ok($user)->withCookie($cookie);
    })
]);

Route::prefix('/user')->middleware([RequiresLogin::class])->group(fn () => [
    Route::get('/{id}', function (Request $request, string $id) {
        $user = User::find((int) $id);

        if ($user === null) {
            return err(Status::HTTP_NOT_FOUND, HttpError::USER_NOT_FOUND);
        }

        return ok($user);
    })->whereNumber('id')
]);
