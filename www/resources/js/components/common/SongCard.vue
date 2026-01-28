<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { Icon } from '.'
import { usePlayerStore } from '@/stores/player'
import { _Song, _Playlist } from '@/types'
import { usePage } from '@inertiajs/vue3'

const props = defineProps<{ song: _Song }>()

const player = usePlayerStore()
const page = usePage()


const dropdownOpen = ref(false)
const menuX = ref(0)
const menuY = ref(0)


const showPlaylists = ref(false)
const playlists = ref<_Playlist[]>([])

async function play() {
    player.currentPlaylist = null
    if (!player.currentTrack || player.currentTrack.uuid !== props.song.uuid) {
        await player.playSong(props.song)
    } else {
        player.togglePlay()
    }
}

function openDropdown(e: MouseEvent) {
    e.preventDefault()
    menuX.value = e.clientX + 6
    menuY.value = e.clientY + 6

    window.dispatchEvent(new CustomEvent('song-context-open', { detail: props.song.uuid }))
    dropdownOpen.value = true
}

function like() {
    player.toggleLike(props.song)
    dropdownOpen.value = false
}

async function addToPlaylist() {
    if (!showPlaylists.value) {
        const allPlaylists = await player.fetchPlaylists(page.props.self.username);

        // check for each playlist if it already contains the song
        const checks = await Promise.all(
            allPlaylists.map(async (playlist) => {
                const hasSong = await player.containsSong(playlist, props.song);
                // temporarily add hasSong to the playlist object
                (playlist as any).hasSong = hasSong;
                return playlist;
            })
        );

        playlists.value = checks;
        showPlaylists.value = true;
    }
}

async function addSongToPlaylist(playlistUuid: string) {
    try {
        await player.addSongToPlaylist(playlistUuid, props.song.uuid)
        dropdownOpen.value = false
        showPlaylists.value = false
    } catch (err) {
        console.error("Failed to add song to playlist", err)
    }
}

function addToQueue() {
    player.addToQueue(props.song)
    dropdownOpen.value = false
}

function handleOtherDropdown(e: Event) {
    const event = e as CustomEvent<string>
    if (event.detail !== props.song.uuid) dropdownOpen.value = false
}


function handleClickOutside() {
    dropdownOpen.value = false
    showPlaylists.value = false
}



onMounted(() => {
    window.addEventListener('song-context-open', handleOtherDropdown)
    window.addEventListener('click', handleClickOutside)
})

onBeforeUnmount(() => {
    window.removeEventListener('song-context-open', handleOtherDropdown)
    window.removeEventListener('click', handleClickOutside)
})
</script>

<template>
    <div class="group relative w-44 cursor-pointer rounded-lg p-3 transition-colors duration-200 hover:bg-gray-500/10 dark:hover:bg-white/10"
        :class="player.currentTrack?.uuid === props.song.uuid && player.isPlaying ? 'bg-gray-500/10 dark:bg-white/10' : ''"
        @contextmenu.prevent="openDropdown">

        <div class="relative mb-3 aspect-square overflow-hidden rounded-md">
            <img :src="props.song.cover_url ?? '/uploads/thumbnails/defaultThumbnail.png'" alt=""
                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105" />
            <button @click.stop="play" class="absolute bottom-2 right-2 flex h-10 w-10 items-center justify-center rounded-full bg-cyan-500 text-white shadow-lg
                       opacity-0 translate-y-3 scale-90 transition-all duration-300 ease-out delay-75
                       group-hover:opacity-100 group-hover:translate-y-0 group-hover:scale-100
                       hover:scale-105 active:scale-95">
                <Icon
                    :name="player.currentTrack?.uuid === props.song.uuid && player.isPlaying ? 'player-pause-filled' : 'player-play-filled'"
                    class="size-6 text-white" />
            </button>
        </div>

        <h3 class="truncate text-sm font-semibold text-black dark:text-white text-center">{{ props.song.title }}</h3>
    </div>

    <ul v-if="dropdownOpen"
        class="fixed bg-white dark:bg-black border border-gray-200 dark:border-gray-500/6 rounded-md shadow-lg py-1 z-50"
        :style="{ left: `${menuX}px`, top: `${menuY}px` }" @click.stop>
        <li @click="like"
            class="flex items-center gap-3 px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer whitespace-nowrap">
            <Icon :name="player.isLiked(props.song) ? 'heart-off' : 'heart-filled'"
                :class="player.isLiked(props.song) ? 'text-black dark:text-white' : 'text-pink-500 dark:text-pink-500'"
                class="size-5" />
            <span>{{ player.isLiked(props.song) ? $t('songCard.unlike') : $t('songCard.like') }}</span>
        </li>

        <li
            class="relative flex items-center gap-3 px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer whitespace-nowrap">
            <div @mouseenter="addToPlaylist" class="flex items-center gap-3">
                <Icon name="square-rounded-plus" class="size-5 text-black dark:text-white" />
                <span>{{ $t('songCard.addToPlaylist') }}</span>
            </div>

            <ul v-if="showPlaylists"
                class="absolute left-full top-0 ml-1 w-fit bg-white dark:bg-black border border-gray-200 dark:border-gray-500/6 rounded-md shadow-lg z-50">

                <li v-for="playlist in playlists" :key="playlist.uuid"
                    @click.stop="!(playlist as any).hasSong && addSongToPlaylist(playlist.uuid)" :class="[
                        'px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer',
                        (playlist as any).hasSong ? 'text-gray-400 dark:text-gray-500 cursor-not-allowed hover:bg-transparent' : ''
                    ]">
                    {{ playlist.name }}
                    <span v-if="(playlist as any).hasSong" class="ml-2 text-xs text-gray-500">✓</span>
                </li>

                <li v-if="playlists.length === 0"
                    class="flex items-center gap-2 px-4 py-2 text-gray-400 dark:text-gray-500 cursor-not-allowed select-none">
                    <Icon name="cancel" class="size-4 text-red-500" />
                    <span class="truncate">{{ $t('songCard.noPlaylistsAvailable') }}</span>
                </li>
            </ul>
        </li>

        <li @click="addToQueue"
            class="flex items-center gap-3 px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer whitespace-nowrap">
            <Icon name="playlist-add" class="size-5 text-black dark:text-white" />
            <span>{{ $t('songCard.addToQueue') }}</span>
        </li>
    </ul>
</template>
