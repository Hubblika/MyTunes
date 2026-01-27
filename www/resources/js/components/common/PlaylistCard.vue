<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { Icon, Button, RenameModal } from '.';
import { router } from '@inertiajs/vue3';
import { usePlayerStore } from '@/stores/player'
import axios from "axios";
import { _Song, _Playlist } from '@/types';


const dropdownRef = ref<HTMLElement | null>(null)
const dropdownOpen = ref(false)

const handleClickOutside = (event: MouseEvent) => {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target as Node)) {
        dropdownOpen.value = false
    }
}

const player = usePlayerStore()

const renamingModal = ref(false)
const renameInput = ref('')

const { playlist } = defineProps<{
    playlist: _Playlist
}>()

const emit = defineEmits<{
    deletePlaylist: [id: string]
    renamePlaylist: [id: string, name: string]
}>()

const selectMenu = (item: string) => {
    dropdownOpen.value = false
    switch (item) {
        case 'delete':
            emit('deletePlaylist', playlist.uuid)
            break
        case 'rename':
            renameInput.value = playlist.name
            renamingModal.value = true
            break
    }
}


async function confirmRename() {
    const name = renameInput.value.trim()
    if (!name) return

    try {
        await axios.put(`/playlists/${playlist.uuid}`, { name })
        player.renamePlaylist(playlist.uuid, name)
    } catch (err) {
        console.error('Failed to rename playlist', err)
    }
}

async function play() {
    const isLikedPlaylist = playlist.uuid === '00000000-0000-0000-0000-000000000000';

    if (isLikedPlaylist) {
        if (!player.likedSongList.length) return;

        const isCurrentPlaylist = player.currentPlaylist === playlist.uuid;

        if (isCurrentPlaylist) {
            player.togglePlay();
        } else {
            const likedPlaylist: _Playlist = {
                ...playlist,
                songs: [...player.likedSongList]
            };

            player.addPlaylistToQueue(likedPlaylist);
        }
    } else if (playlist) {
        if (player.currentPlaylist === playlist.uuid) {
            player.togglePlay();
        } else {
            player.addPlaylistToQueue(playlist);
        }
    }
}

onMounted(async () => {
    document.addEventListener('click', handleClickOutside);
    await player.fetchLikedSongs()
})

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside)
})
</script>

<template>
    <div :class="player.currentPlaylist === playlist.uuid && player.isPlaying
        ? 'bg-gray-500/10 dark:bg-white/10'
        : ''" class="flex items-center gap-3 p-2 rounded-md hover:bg-gray-500/10 dark:hover:bg-white/10 transition">
        <div class="relative size-12 rounded bg-gray-400/30 dark:bg-white/20 overflow-hidden shrink-0 group cursor-pointer"
            @click="() => router.visit(`/playlist/${playlist.uuid}`)">
            <img :src="'/uploads/thumbnails/defaultThumbnail.png'"
                class="w-full h-full object-cover transition-opacity duration-150 group-hover:opacity-50" />
            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-150"
                @click.stop="play">
                <Icon :name="player.currentPlaylist === playlist.uuid && player.isPlaying
                    ? 'player-pause-filled'
                    : 'player-play-filled'"
                    class="size-6 text-white hover:scale-110 transition-transform cursor-pointer" />
            </div>
        </div>

        <div class="min-w-0 flex flex-col grow cursor-pointer"
            @click="() => router.visit(`/playlist/${playlist.uuid}`)">
            <span class="text-sm font-medium truncate">{{ playlist.name }}</span>
            <span class="text-xs opacity-60 truncate">
                {{ playlist.uuid === '00000000-0000-0000-0000-000000000000' ? player.likedCount : playlist.songs_count
                ?? 0 }}
                {{ $t('sidebar.playlistNumber') }}
            </span>
        </div>

        <div v-if="playlist.uuid !== '00000000-0000-0000-0000-000000000000'" class="relative" ref="dropdownRef">
            <Button @click="dropdownOpen = !dropdownOpen"
                class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700">
                <Icon name="dots-vertical" class="size-5 transition-transform duration-150 group-hover:scale-110" />
            </Button>

            <ul v-if="dropdownOpen"
                class="absolute mt-2 bg-white dark:bg-black border border-gray-200 dark:border-gray-500/6 rounded-md shadow-lg py-1 z-50 right-0">
                <li @click="() => selectMenu('rename')"
                    class="flex items-center gap-3 px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer whitespace-nowrap">
                    <Icon name="pencil" class="size-5 text-black dark:text-white" />
                    <span>{{ $t('sidebar.renamePlaylistButton') }}</span>
                </li>
                <li @click="() => selectMenu('delete')"
                    class="flex items-center gap-3 px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer whitespace-nowrap">
                    <Icon name="trash" class="size-5 text-red-500" />
                    <span>{{ $t('sidebar.deletePlaylistButton') }}</span>
                </li>
            </ul>
        </div>
    </div>

    <RenameModal v-model="renamingModal" v-model:inputValue="renameInput" :title="$t('sidebar.renamePlaylistButton')"
        @save="confirmRename" />
</template>
