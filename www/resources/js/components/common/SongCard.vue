<script setup lang="ts">
import song from '@/routes/song';
import { Icon } from '.'
import { usePlayerStore } from '@/stores/player'
import { _Song } from '@/types';

const props = defineProps<{
    song: _Song 
}>()

const player = usePlayerStore()

function play() {
    player.playSong(props.song)
}
</script>

<template>
    <div class="group w-44 cursor-pointer rounded-lg
           bg-transparent p-3
           transition-colors duration-200
           hover:bg-white dark:hover:bg-black">
        <div class="relative mb-3 aspect-square overflow-hidden rounded-md">
            <img :src="props.song.covereurl ?? '/uploads/thumbnails/playlist/defaultThumbnail.png'" alt="" class="h-full w-full object-cover
               transition-transform duration-300
               group-hover:scale-105" />

            <button @click="play" class="absolute bottom-2 right-2
               flex h-10 w-10 items-center justify-center
               rounded-full bg-cyan-500 text-white shadow-lg

               opacity-0 translate-y-3 scale-90
               transition-all duration-300 ease-out delay-75

               group-hover:opacity-100
               group-hover:translate-y-0
               group-hover:scale-100

               hover:scale-105 active:scale-95">
                <Icon name="player-play-filled" />
            </button>
        </div>

        <h3 class="truncate text-sm font-semibold text-pink-500 text-center">
            {{ props.song.title }}
        </h3>
    </div>
</template>
