<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { Icon, Button, RenameModal } from '.';
import { router } from '@inertiajs/vue3';
import { Playlist } from '@/lib/types';
import { usePlayerStore } from '@/stores/player'
import axios from "axios";
import { _Song } from '@/types';


const dropdownRef = ref<HTMLElement | null>(null)
const dropdownOpen = ref(false)

const handleClickOutside = (event: MouseEvent) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target as Node)) {
    dropdownOpen.value = false
  }
}

const player = usePlayerStore()
const likes = ref<string[]>([])
const likedSongs = ref<_Song[]>([])

const renamingModal = ref(false)
const renameInput = ref('')

const { playlist } = defineProps<{
    playlist: Playlist
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


function confirmRename() {
    emit('renamePlaylist', playlist.uuid, renameInput.value)
}

async function play() {
    if (playlist.uuid === '00000000-0000-0000-0000-000000000000') {
        await fetchLikes()
        await fetchLikedSongs()

        if (!likedSongs.value.length) return

        const isCurrentPlaylist = player.currentPlaylist === playlist.uuid

        if (isCurrentPlaylist) {
            player.togglePlay()
        } else {
            player.emptyQueue()
            likedSongs.value.forEach(song => player.addToQueue(song, playlist.uuid))
            player.currentIndex = 0
            player.isPlaying = true
            player.currentPlaylist = playlist.uuid
            await player.fetchLiked()
        }
    }
}

async function fetchLikes() {
    const response = await axios.get("/api/like")
    likes.value = response.data.likes
}

async function fetchLikedSongs() {
    if (!likes.value?.length) return
    try {
        const requests = likes.value.map(uuid => axios.get(`/api/songs/${uuid}`))
        const responses = await Promise.all(requests)
        likedSongs.value = responses.map(r => r.data.data).filter(Boolean)
    } catch {
        likedSongs.value = []
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
    fetchLikes()
    fetchLikedSongs()
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<template>
    <div :class="player.currentPlaylist === playlist.uuid && player.isPlaying
        ? 'bg-gray-500/10 dark:bg-white/10'
        : ''"
        class="flex items-center gap-3 p-2 rounded-md cursor-pointer hover:bg-gray-500/10 dark:hover:bg-white/10 transition"
        @click="() => router.visit(`/playlist/${playlist.uuid}`)">

        <div class="relative size-12 rounded bg-gray-400/30 dark:bg-white/20 overflow-hidden shrink-0 group">
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

        <div class="min-w-0 flex flex-col grow">
            <span class="text-sm font-medium truncate">
                {{ playlist.name }}
            </span>
            <span class="text-xs opacity-60 truncate">
                {{ playlist.uuid === '00000000-0000-0000-0000-000000000000' ? likedSongs.length : '0' }} {{
                    $t('sidebar.playlistNumber') }}
            </span>
        </div>

        <div v-if="playlist.uuid !== '00000000-0000-0000-0000-000000000000'" class="relative" ref="dropdownRef">
            <Button @click="dropdownOpen = !dropdownOpen" class="group transition-all duration-150">
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
