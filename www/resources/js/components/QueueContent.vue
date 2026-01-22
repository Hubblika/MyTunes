<script setup lang="ts">
import { storeToRefs } from "pinia";
import { usePlayerStore } from "@/stores/player";
import QueueSong from "./common/QueueSong.vue";

const player = usePlayerStore();
const { queue } = storeToRefs(player);
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
            <QueueSong v-for="(song, index) in queue" :key="song.uuid" :index="index + 1" :song="song" />
        </div>
    </section>
</template>
