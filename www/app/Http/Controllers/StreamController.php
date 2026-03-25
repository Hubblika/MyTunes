<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Illuminate\Support\Facades\Storage;

class StreamController extends Controller
{
    public function stream($uuid)
    {
        $song = Song::find($uuid);
        abort_unless($song, 404);

        $path = "songs/{$song->file_name}";
        $disk = Storage::disk('local');
        $fullPath = $disk->path($path);

        abort_unless($disk->exists($path), 404);

        $size = filesize($fullPath);
        $mime = 'audio/mpeg';

        $headers = [
            'Content-Type' => $mime,
            'Accept-Ranges' => 'bytes',
        ];

        $range = request()->header('Range');
        if ($range) {
            preg_match('/bytes=(\d+)-(\d+)?/', $range, $matches);
            $start = (int) $matches[1];
            $end = isset($matches[2]) ? (int) $matches[2] : $size - 1;
            $length = $end - $start + 1;

            $headers += [
                'Content-Range' => "bytes $start-$end/$size",
                'Content-Length' => $length,
            ];

            return response()->stream(function () use ($fullPath, $start, $length) {
                $handle = fopen($fullPath, 'rb');
                fseek($handle, $start);
                echo fread($handle, $length);
                fclose($handle);
            }, 206, $headers);
        }

        $headers['Content-Length'] = $size;

        return response()->stream(function () use ($fullPath) {
            readfile($fullPath);
        }, 200, $headers);
    }
}
