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
                'artist' => 'TheFatRat',
                'duration' => 215,
                'release_date' => '2020-03-15',
            ],
            [
                'title' => 'Mayday',
                'artist' => 'TheFatRat',
                'duration' => 190,
                'release_date' => '2019-07-22',
            ],
            [
                'title' => 'Rise Up',
                'artist' => 'TheFatRat',
                'duration' => 185,
                'release_date' => '2021-01-10',
            ],
            [
                'title' => 'Unity',
                'artist' => 'TheFatRat',
                'duration' => 260,
                'release_date' => '2018-11-05',
            ],
            [
                'title' => 'Xenogenesis',
                'artist' => 'TheFatRat',
                'duration' => 255,
                'release_date' => '2022-06-30',
            ],
            [
                'title' => 'Hellcat',
                'artist' => 'NCS',
                'duration' => 255,
                'release_date' => '2022-06-30',
            ],
            [
                'title' => 'Heroes Tonight',
                'artist' => 'NCS',
                'duration' => 255,
                'release_date' => '2022-06-30',
            ],
            [
                'title' => 'Mortals',
                'artist' => 'NCS',
                'duration' => 255,
                'release_date' => '2022-06-30',
            ],
            [
                'title' => 'On & On',
                'artist' => 'NCS',
                'duration' => 255,
                'release_date' => '2022-06-30',
            ],
        ];

        foreach ($songs as $song) {
            DB::table('songs')->insert([
                'uuid' => Str::uuid(),
                'title' => $song['title'],
                'artist' => $song['artist'],
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
