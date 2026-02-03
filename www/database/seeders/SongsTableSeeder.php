<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
                'album' => 'Parallax',
                'file_name' => 'FLY AWAY.mp3',
                'duration' => 215,
                'release_date' => '2020-03-15',
            ],
            [
                'title' => 'Mayday',
                'artist' => 'TheFatRat',
                'album' => 'Parallax',
                'file_name' => 'MAYDAY.mp3',
                'duration' => 190,
                'release_date' => '2019-07-22',
            ],
            [
                'title' => 'Rise Up',
                'artist' => 'TheFatRat',
                'album' => 'Rising Sun',
                'file_name' => 'RISE UP.mp3',
                'duration' => 185,
                'release_date' => '2021-01-10',
            ],
            [
                'title' => 'Unity',
                'artist' => 'TheFatRat',
                'album' => 'Unity',
                'file_name' => 'UNITY.mp3',
                'duration' => 260,
                'release_date' => '2018-11-05',
            ],
            [
                'title' => 'Xenogenesis',
                'artist' => 'TheFatRat',
                'album' => 'Xenogenesis',
                'file_name' => 'XENOGENESIS.mp3',
                'duration' => 255,
                'release_date' => '2022-06-30',
            ],
            [
                'title' => 'Hellcat',
                'artist' => 'NCS',
                'album' => 'NCS: The Best of 2014',
                'file_name' => 'Hellcat.mp3',
                'duration' => 255,
                'release_date' => '2014-01-01',
            ],
            [
                'title' => 'Heroes Tonight',
                'artist' => 'NCS',
                'album' => 'NCS: Heroes',
                'file_name' => 'Heroes Tonight.mp3',
                'duration' => 255,
                'release_date' => '2015-12-01',
            ],
            [
                'title' => 'Mortals',
                'artist' => 'NCS',
                'album' => 'NCS: Infinity',
                'file_name' => 'Mortals.mp3',
                'duration' => 255,
                'release_date' => '2016-08-01',
            ],
            [
                'title' => 'On & On',
                'artist' => 'NCS',
                'album' => 'NCS: On & On',
                'file_name' => 'On & On.mp3',
                'duration' => 255,
                'release_date' => '2014-07-01',
            ],
        ];

        foreach ($songs as $song) {
            DB::table('songs')->insert(['uuid' => Str::uuid(),
            'title' => $song['title'],
            'artist' => $song['artist'],
            'album' => $song['album'],
            'file_name' => $song['file_name'],
            'cover_url' => '/uploads/thumbnails/defaultThumbnail.png',
            'date' => $song['release_date'],
            'created_at' => now(),
            'duration' => $song['duration'], 'genre' => 'Electronic',
            ]);
        }
    }
}
