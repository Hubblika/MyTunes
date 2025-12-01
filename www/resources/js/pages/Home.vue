<script setup lang="ts">
import { Icon, Preview } from '@/components';
import { MainLayout } from '@/layouts';
import type { Playlist as IPlaylist, Song } from '@/types';

const temp__playlists: IPlaylist[] = [
    {
        uuid: '497dcba3-ecbf-4587-a2dd-5eb0665e6880',
        creator_id: 0,
        name: 'Liked songs',
        is_album: false,
        created_at: new Date().toISOString(),
        updated_at: new Date().toISOString()
    }
];

const temp__recent: Song[] = [
    {
        uuid: '497dcba3-ecbf-4587-a2dd-5eb0665e6880',
        title: 'Example song 1',
        created_at: new Date().toISOString(),
        duration: 0,
        is_explicit: false
    },
    {
        uuid: '50e14f43-dd4e-412f-864d-78943ea28d91',
        title: 'Example song 2',
        created_at: new Date().toISOString(),
        duration: 0,
        is_explicit: false
    },
    {
        uuid: '7edb3b2e-869c-485b-af70-76a934e0fcfd',
        title: 'Example song 3',
        created_at: new Date().toISOString(),
        duration: 0,
        is_explicit: false
    },
    {
        uuid: '67e32b59-3348-4dc3-9645-75c60b6f50cc',
        title: 'Example song 4',
        created_at: new Date().toISOString(),
        duration: 0,
        is_explicit: false
    },
    {
        uuid: '4c8f6d82-e4c6-4478-92eb-d9342500f006',
        title: 'Example song 5',
        created_at: new Date().toISOString(),
        duration: 0,
        is_explicit: false
    }
];
</script>

<template>
    <MainLayout>
        <section class="flex flex-col gap-2">
            <h1 class="font-bold text-2xl">
                My playlists
                <button class="h-full aspect-square inline-flex translate-y-1.75 cursor-pointer">
                    <Icon name="plus" class="h-full"></Icon>
                </button>
            </h1>
            <ul class="w-full overflow-x-scroll overflow-y-hidden flex gap-4">
                <Preview :self="playlist" v-for="playlist in temp__playlists" :key="playlist.uuid"></Preview>
            </ul>
        </section>
        <section class="flex flex-col gap-2">
            <h1 class="font-bold text-2xl">Recent listens</h1>
            <ul v-if="temp__recent.length > 0" class="w-full overflow-x-scroll overflow-y-hidden flex gap-4">
                <Preview :self="song" v-for="song in temp__recent" :key="song.uuid"></Preview>
            </ul>
            <span class="" v-else>Nothing here yet</span>
        </section>
    </MainLayout>
</template>
