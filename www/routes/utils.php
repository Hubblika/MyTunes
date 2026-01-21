<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

const LIMIT = 40;

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
 * @param ?array $info Optional additional information
 * @return JsonResponse The json response
 */
function err(int $status, ?array $info = null): JsonResponse {
    return response()->json(
        [
            'error' => [
                'status' => $status,
                'info' => clean($info)
            ]
        ],
        $status
    );
}



/**
 * Generates a random string of `$n` length
 * @param int $n Length of the result string
 * @return string Generated username
 */
function generate_username(int $n = 16) {
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $result = '';

    for ($i = 0; $i < $n; $i++) {
        $result .= $alphabet[random_int(0, strlen($alphabet) - 1)];
    }

    return $result;
}

/**
 * Removes all entries in an object where the value is `null` 
 * @param ?object $data Object
 * @return mixed
 */
function clean(mixed $data) {
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
