<script setup lang="ts">
import { ref, computed } from 'vue';
import { Icon, Button } from '.';
import { _Song } from '@/types';
import { usePlayerStore } from '@/stores/player';
import { usePage } from '@inertiajs/vue3';

const props = defineProps<{
    index: number;
    song: _Song;
    playlistUuid: string;
}>()

const player = usePlayerStore();
const page = usePage();

const dropdownOpen = ref(false);

function handleClickOutside() {
    dropdownOpen.value = false;
}

async function playSong() {
    const current = player.currentTrack;
    player.currentPlaylist = props.playlistUuid;

    if (!current || current.uuid !== props.song.uuid) {
        await player.playSong(props.song);
    } else {
        await player.togglePlay();
    }
}

function toggleDropdown(e: MouseEvent) {
    e.stopPropagation();
    dropdownOpen.value = !dropdownOpen.value;
}
</script>

<template>
    <div class="flex items-center gap-3 p-3 rounded-lg bg-white/10 dark:bg-black/10
           hover:bg-white/20 dark:hover:bg-black/20 transition-colors cursor-pointer" @click="playSong">
        <img :src="song.cover_url" alt="cover" class="w-12 h-12 rounded object-cover" />

        <div class="flex-1 min-w-0">
            <div class="truncate font-medium text-black dark:text-white">{{ song.title }}</div>
            <div class="truncate text-sm text-neutral-500 dark:text-neutral-400">{{ song.artist }}</div>
        </div>

        <Button class="flex items-center justify-center w-10 h-10 p-0 rounded-full" @click.stop="playSong">
            <Icon :name="player.currentTrack?.uuid === song.uuid && player.isPlaying
                ? 'player-pause-filled'
                : 'player-play-filled'" class="text-white size-8" />
        </Button>

        <Button class="flex items-center justify-center w-10 h-10 p-0 rounded-full" @click.stop="toggleDropdown">
            <Icon name="dots-vertical" class="text-black dark:text-white size-6" />
        </Button>

        <ul v-if="dropdownOpen" class="absolute right-4 top-16 z-50
               bg-white/20 dark:bg-black/20 backdrop-blur-md
               border border-black/10 dark:border-white/10
               rounded-2xl shadow-lg py-2 min-w-[180px]">
            <li class="px-4 py-2 cursor-pointer hover:bg-white/30 dark:hover:bg-black/30">Like</li>
            <li class="px-4 py-2 cursor-pointer hover:bg-white/30 dark:hover:bg-black/30">Add to Playlist</li>
            <li class="px-4 py-2 cursor-pointer hover:bg-white/30 dark:hover:bg-black/30">Add to Queue</li>
            <li v-if="props.playlistUuid !== '00000000-0000-0000-0000-000000000000'"
                class="px-4 py-2 cursor-pointer hover:bg-white/30 dark:hover:bg-black/30">
                Remove from Playlist
            </li>
        </ul>
    </div>
</template>
