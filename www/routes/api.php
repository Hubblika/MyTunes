<?php

use App\Models\Session;
use App\Models\User;
use Illuminate\Http\Request;

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
