<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class SongsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $songs = [
            [
                'title' => 'Fly Away',
                'duration' => 215,
            ],
            [
                'title' => 'Mayday',
                'duration' => 190,
            ],
            [
                'title' => 'Rise Up',
                'duration' => 185,
            ],
            [
                'title' => 'Unity',
                'duration' => 260,
            ],
            [
                'title' => 'Xenogenesis',
                'duration' => 255,
            ],
        ];

        foreach ($songs as $song) {
            DB::table('songs')->insert([
                'id' => Str::uuid(),
                'title' => $song['title'],
                'artist' => 'TheFatRat',
                'url' => '/audio/' . $song['title'] . '.mp3',
                'cover_url' => 'uploads/thumbnails/defaultThumbnail.png',
                'created_at' => now(),
                'duration' => $song['duration'],
                'genre' => 'Electronic',
            ]);
        }
    }
}
