<?php

use Illuminate\Http\JsonResponse;

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
 * Shorthand for returning json data to the client
 * @param int $status Status code
 * @param mixed $data Deserializable data
 * @return JsonResponse The json response
 */
function ok(mixed $data, int $status = 200): JsonResponse {
    return response()->json(['data' => clean($data)], $status);
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
            'error' => clean([
                'status' => $status,
                'name' => $error->name,
                'message' => $message
            ])
        ],
        $status
    );
}



function generate_username(int $length = 16) {
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $result = '';

    for ($i = 0; $i < $length; $i++) {
        $result .= $alphabet[random_int(0, strlen($alphabet) - 1)];
    }

    return $result;
}

function clean($data) {
    if (is_array($data)) {
        return array_filter(
            array_map('clean', $data),
            fn($v) => $v !== null
        );
    }

    if (is_object($data)) {
        foreach ($data as $k => $v) {
            if ($v === null) {
                unset($data->$k);
            } else {
                $data->$k = clean($v);
            }
        }
    }

    return $data;
}

function is_valid_email(?string $email) {
    return $email === null || !preg_match('/^[a-z-.]+@([a-z-]+.)+[a-z-]{2,6}$/i', $email);
}

function is_valid_password(?string $password) {
    return $password === null || strlen($password) < 8;
}
