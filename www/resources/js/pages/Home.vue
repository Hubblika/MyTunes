<script setup lang="ts">
import { Preview } from '@/components';
import { MainLayout } from '@/layouts';
import type { Playlist as IPlaylist, Song } from '@/types';

const temp__playlists: IPlaylist[] = [
    {
        id: 0,
        creator_id: '1',
        name: 'Liked songs',
        is_album: false,
        created_at: '',
        updated_at: ''
    }
];

const temp__recent: Song[] = [];
</script>

<template>
    <MainLayout>
        <section class="flex flex-col gap-2">
            <h1 class="font-bold text-2xl">My playlists</h1>
            <ul class="w-full overflow-x-scroll overflow-y-hidden flex gap-4">
                <Preview :self="playlist" v-for="playlist in temp__playlists" :key="playlist.id"></Preview>
            </ul>
        </section>
        <section class="flex flex-col gap-2">
            <h1 class="font-bold text-2xl">Recent listens</h1>
            <ul v-if="temp__recent.length > 0" class="w-full overflow-x-scroll overflow-y-hidden flex gap-4">
                <Preview :self="song" v-for="song in temp__recent" :key="song.id"></Preview>
            </ul>
            <span class="" v-else>Nothing here yet</span>
        </section>
    </MainLayout>
</template>
