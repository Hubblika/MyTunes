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
                'release_date' => '2020-03-15',
            ],
            [
                'title' => 'Mayday',
                'duration' => 190,
                'release_date' => '2019-07-22',
            ],
            [
                'title' => 'Rise Up',
                'duration' => 185,
                'release_date' => '2021-01-10',
            ],
            [
                'title' => 'Unity',
                'duration' => 260,
                'release_date' => '2018-11-05',
            ],
            [
                'title' => 'Xenogenesis',
                'duration' => 255,
                'release_date' => '2022-06-30',
            ],
        ];

        foreach ($songs as $song) {
            DB::table('songs')->insert([
                'uuid' => Str::uuid(),
                'title' => $song['title'],
                'artist' => 'TheFatRat',
                'url' => '/audio/' . $song['title'] . '.mp3',
                'cover_url' => '/uploads/thumbnails/defaultThumbnail.png',
                'date' => $song['release_date'],
                'created_at' => now(),
                'duration' => $song['duration'],
                'genre' => 'Electronic',
            ]);
        }
    }
}
