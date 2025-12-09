<?php

use App\Http\Middleware\RequiresJson;
use App\Http\Middleware\RequiresLogin;
use App\Models\OrderedSong;
use App\Models\Playlist;
use App\Models\Session;
use App\Models\Song;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;

/**
 * Enum containing common http errors to be handled by the Vue frontend
 */
enum ApiError {
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
    case SONG_NOT_FOUND;
    case PLAYLIST_NOT_FOUND;
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
    return hash('sha256', $data);
}

/**
 * Gets the current session being used to make the request, or `null` if `AUTH_TOKEN` isn't in the `cookie` header
 * @param Request $request
 * @return ?Session
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

function get_playlist_with_songs(string $uuid) {
    $playlist = Playlist::find($uuid);

    if ($playlist === null) {
        return null;
    }

    $songs = [];

    foreach ($playlist->songs as $ordered) {
        // TODO: make this better because it sucks
        array_push($songs, $ordered->song);
    }

    return [
        'playlist' => $playlist->makeHidden('songs'),
        'songs' => $songs
    ];
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
 * @param ApiError $error The HTTP error kind which will determine what data is displayed
 * @return JsonResponse The json response
 */
function err(int $status, ApiError $error = ApiError::UNKNOWN, ?string $message = null): JsonResponse {
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
    Session::create([
        'token_hash' => sha256($bytes),
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



/// Creates and returns a new `User`, creating a `Session`
Route::post('/account/register', function (Request $request) {
    $email = $request->json('email');
    $password = $request->json('password');

    if ($email === null || !preg_match('/^[a-z-.]+@([a-z-]+.)+[a-z-]{2,6}$/i', $email)) {
        return err(400, ApiError::INVALID_EMAIL);
    }

    if ($password === null || strlen($password) < 8) {
        return err(400, ApiError::INVALID_PASSWORD);
    }

    $user_id = User::create([
        'email' => $email,
        'password_hash' => sha256($password)
    ])->getKey();
    $user = User::find($user_id);

    $cookie = new_session($user, $request);

    return ok($user, 201)->withCookie($cookie);
})->middleware([RequiresJson::class]);

/// Logs into an existing `User`, creating a `Session`
Route::post('/account/login', function (Request $request) {
    $email = $request->json('email');
    $password = $request->json('password');

    if ($email === null || !preg_match('/^[a-z-.]+@([a-z-]+.)+[a-z-]{2,6}$/i', $email)) {
        return err(400, ApiError::INVALID_EMAIL);
    }

    if ($password === null || strlen($password) < 8) {
        return err(400, ApiError::INVALID_PASSWORD);
    }

    $user = User::where('email', $email)->first();

    if ($user === null) {
        return err(404, ApiError::USER_NOT_FOUND);
    }

    if (sha256($password) !== $user->password_hash) {
        return err(401, ApiError::INCORRECT_PASSWORD);
    }

    $cookie = new_session($user, $request);

    return ok($user)->withCookie($cookie);
})->middleware([RequiresJson::class]);



/// Creates a new `Song` if the user is authorized
Route::put('/song', function (Request $request) {
    $user = current_session($request)->user;

    if ($user->role === 'User') {
        return err(403, ApiError::UNAUTHORIZED);
    }

    $title = $request->input('title');
    $created_at = $request->input('created_at');
    $duration = $request->input('duration');
    $explicit = $request->input('is_explicit');
    
    $audio = $request->file('audio.mp3');
    $thumbnail = $request->file('thumbnail.png');

    if (!$title || !$duration || !$audio || !$thumbnail) {
        // TODO: handle individually
        return err(400);
    }

    $song_uuid = Song::create([
        'title' => '',
        'created_at' => $created_at || Date::now(),
        'duration' => $duration,
        'is_explicit' => $explicit || false
    ])->getKey();

    Storage::disk('public')->put('uploads/audio/{$song_uuid}.mp3', $audio);
    Storage::disk('public')->put('uploads/thumbnails/song/{$song_uuid}.png', $thumbnail);

    $song = Song::find($song_uuid);
    return ok($song);
})->middleware([RequiresLogin::class]);

Route::delete('/song/{uuid}', function (Request $request, string $uuid) {
    $user = current_session($request)->user;
    $song = Song::find($uuid);

    if (!$song) {
        return err(404, ApiError::SONG_NOT_FOUND);
    }

    if ($user->role !== 'Admin') {
        return err(403, ApiError::UNAUTHORIZED);
    }

    // TODO: create `ApiSuccess` enum?
    return ok(1);
})->middleware([RequiresLogin::class])->whereUuid('uuid');



/// Returns the currently logged-in user
Route::get('/user/@me', function (Request $request) {
    $user = current_session($request)?->user;
    return ok($user);
});

Route::get('/user/{id}', function (Request $request, string $id) {
    $user = User::find((int) $id);

    if ($user === null) {
        return err(404, ApiError::USER_NOT_FOUND);
    }

    return ok($user);
})->middleware([RequiresLogin::class])->whereNumber('id');
