<script setup lang="ts">
import { Icon } from '.'
import { _Song } from '@/types';
import { usePlayerStore } from '@/stores/player'

const props = defineProps<{
    index: number;
    song: _Song;
}>()

const player = usePlayerStore()

async function play() {
    const current = player.currentTrack
    player.currentPlaylist = null

    if (!current || current.uuid !== props.song.uuid) {
        await player.playSong(props.song)
    } else {
        await player.togglePlay()
    }
}

function formatDuration(seconds: number) {
    const m = Math.floor(seconds / 60)
    const s = seconds % 60
    return `${m}:${s.toString().padStart(2, "0")}`
}
</script>

<template>
    <div @click="play" :class="player.currentTrack?.uuid === props.song.uuid && player.isPlaying
        ? 'bg-gray-500/10 dark:bg-white/10'
        : ''" class="group grid grid-cols-[32px_48px_1fr_100px]
       items-center gap-4 px-4 py-2 rounded-md
       text-sm text-neutral-400 hover:bg-gray-500/10 dark:hover:bg-white/10 cursor-pointer">

        <div class="text-right group-hover:hidden">
            {{ index }}
        </div>
        <div class="hidden group-hover:flex justify-end">
            <Icon @click.stop="play" :name="player.currentTrack?.uuid === props.song.uuid && player.isPlaying
                ? 'player-pause-filled'
                : 'player-play-filled'" class="size-6 text-white" />
        </div>

        <img :src="song.cover_url" alt="cover" class="w-10 h-10 rounded object-cover" />

        <div class="min-w-0">
            <div class="text-black dark:text-white font-medium truncate">
                {{ song.title }}
            </div>
            <div class="truncate">
                {{ song.artist }}
            </div>
        </div>

        <div class="justify-self-end text-right tabular-nums">
            {{ formatDuration(song.duration) }}
        </div>
    </div>
</template>