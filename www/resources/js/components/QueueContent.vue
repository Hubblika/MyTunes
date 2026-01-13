<script setup lang="ts">
import PlaylistSong from './common/QueueSong.vue';

type Song = {
    id: number
    cover: string
    title: string
    artist: string
    addedAt: string
    duration: string
}

const songs: Song[] = [
    {
        id: 1,
        cover: '/uploads/thumbnails/defaultThumbnail.png',
        title: 'Blinding Lights',
        artist: 'The Weeknd',
        addedAt: '3 days ago',
        duration: '3:20'
    },
    {
        id: 2,
        cover: '/uploads/thumbnails/defaultThumbnail.png',
        title: 'Levitating',
        artist: 'Dua Lipa',
        addedAt: '1 week ago',
        duration: '3:23'
    },
    {
        id: 3,
        cover: '/uploads/thumbnails/defaultThumbnail.png',
        title: 'Save Your Tears',
        artist: 'The Weeknd',
        addedAt: '2 weeks ago',
        duration: '3:35'
    }
]
</script>

<template>
    <section class="flex flex-col h-full gap-4">
        <header class="px-4">
            <h1 class="text-2xl font-bold text-black dark:text-white">
                {{ $t('queue.title') }}
            </h1>
        </header>

        <div class="grid grid-cols-[32px_48px_1fr_100px] gap-4 px-4 py-2
         text-xs uppercase tracking-widest
         text-black border-black dark:text-neutral-400
         border-b dark:border-neutral-800">
            <div class="text-right">#</div>
            <div></div>
            <div>{{ $t('queue.headerTitle') }}</div>
            <div class="justify-self-end">{{ $t('queue.headerDuration') }}</div>
        </div>

        <div class="flex flex-col overflow-y-auto">
            <PlaylistSong v-for="(song, index) in songs" :key="song.id" :index="index + 1" :cover="song.cover"
                :title="song.title" :artist="song.artist" :added-at="song.addedAt" :duration="song.duration" />
        </div>
    </section>
</template>
