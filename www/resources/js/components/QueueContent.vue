<script setup lang="ts">
import { storeToRefs } from "pinia";
import { usePlayerStore } from "@/stores/player";
import PlaylistSong from "./common/QueueSong.vue";

const player = usePlayerStore();
const { queue } = storeToRefs(player);

function formatDuration(seconds: number) {
    const m = Math.floor(seconds / 60);
    const s = seconds % 60;
    return `${m}:${s.toString().padStart(2, "0")}`;
}
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
            <PlaylistSong v-for="(song, index) in queue" :key="song.uuid" :index="index + 1" :cover="song.cover_url"
                :title="song.title" :artist="song.artist" :duration="formatDuration(song.duration)" />
        </div>
    </section>
</template>
