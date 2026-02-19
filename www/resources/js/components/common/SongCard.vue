<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount, nextTick } from 'vue'
import { Icon } from '.'
import { usePlayerStore } from '@/stores/player'
import { _Song, _Playlist } from '@/types'
import { usePage } from '@inertiajs/vue3'

const props = defineProps<{ song: _Song }>()

const player = usePlayerStore()
const page = usePage()


const dropdownRef = ref<HTMLElement | null>(null)
const dropdownOpen = ref(false)
const menuX = ref(0)
const menuY = ref(0)
const OFFSET = 6


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

    menuX.value = e.clientX + OFFSET
    menuY.value = e.clientY + OFFSET

    dropdownOpen.value = true

    nextTick(() => {
        if (!dropdownRef.value) return

        const { innerWidth, innerHeight } = window
        const rect = dropdownRef.value.getBoundingClientRect()

        if (menuX.value + rect.width > innerWidth) {
            menuX.value = e.clientX - rect.width - OFFSET
        }

        if (menuY.value + rect.height > innerHeight) {
            menuY.value = e.clientY - rect.height - OFFSET
        }

        menuX.value = Math.max(OFFSET, menuX.value)
        menuY.value = Math.max(OFFSET, menuY.value)
    })

    window.dispatchEvent(
        new CustomEvent('song-context-open', { detail: props.song.uuid })
    )
}

function like() {
    player.toggleLike(props.song)
    dropdownOpen.value = false
}

async function addToPlaylist() {
    if (!showPlaylists.value) {
        const allPlaylists = await player.fetchPlaylists(page.props.self.username);

        const checks = await Promise.all(
            allPlaylists.map(async (playlist) => {
                const hasSong = await player.containsSong(playlist, props.song);
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
    <div class="group relative w-36 cursor-pointer rounded-lg transition-colors duration-200"
        :class="player.currentTrack?.uuid === props.song.uuid && player.isPlaying ? 'bg-gray-500/10 dark:bg-white/10' : ''">

        <!-- Song Cover -->
        <div class="relative mb-3 aspect-square overflow-hidden rounded-md">
            <img :src="props.song.cover_url ?? '/uploads/thumbnails/defaultThumbnail.png'" alt=""
                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105" />

            <!-- Buttons overlay -->
            <div class="absolute bottom-2 left-2 right-2 flex justify-between items-center">
                <!-- 3-dot menu button (left) -->
                <button @click.stop="openDropdown" class="flex h-10 w-10 items-center justify-center rounded-full
             bg-gray-200/80 dark:bg-black/50
             text-black dark:text-white shadow-lg
             hover:bg-gray-300/80 dark:hover:bg-black/70
             transition-colors duration-200">
                    <Icon name="dots-vertical" class="size-5" />
                </button>

                <!-- Play button (right) -->
                <button @click.stop="play" class="flex h-10 w-10 items-center justify-center rounded-full bg-cyan-500 text-white shadow-lg
             hover:scale-105 active:scale-95 transition-transform duration-200">
                    <Icon
                        :name="player.currentTrack?.uuid === props.song.uuid && player.isPlaying ? 'player-pause-filled' : 'player-play-filled'"
                        class="size-6 text-white" />
                </button>
            </div>
        </div>

        <!-- Song Title -->
        <h3 class="truncate text-sm font-semibold text-black dark:text-white text-center">{{ props.song.title }}</h3>
    </div>

    <Teleport to="body">
        <ul ref="dropdownRef" v-if="dropdownOpen" :style="{ left: `${menuX}px`, top: `${menuY}px` }" class="fixed text-black dark:text-white
         bg-white/20 dark:bg-black/20
         backdrop-blur-md
         border border-black/10 dark:border-white/10
         rounded-2xl shadow-lg py-2 z-50
         min-w-[200px] whitespace-nowrap
         transition-all duration-200" @click.stop>
            <li @click="like" class="flex items-center gap-3 px-4 py-2 rounded-lg
           cursor-pointer hover:bg-white/30 dark:hover:bg-black/30
           transition-colors duration-150">
                <Icon :name="player.isLiked(props.song) ? 'heart-off' : 'heart-filled'"
                    :class="player.isLiked(props.song) ? 'text-black dark:text-white' : 'text-pink-500 dark:text-pink-500'"
                    class="size-5" />
                <span>{{ player.isLiked(props.song) ? $t('songCard.unlike') : $t('songCard.like') }}</span>
            </li>

            <li class="relative flex items-center gap-3 px-4 py-2 rounded-lg
           cursor-pointer hover:bg-white/30 dark:hover:bg-black/30
           transition-colors duration-150">
                <div @mouseenter="addToPlaylist" class="flex items-center gap-3">
                    <Icon name="square-rounded-plus" class="size-5 text-black dark:text-white" />
                    <span>{{ $t('songCard.addToPlaylist') }}</span>
                </div>

                <ul v-if="showPlaylists" class="absolute top-0 left-full translate-x-1
             bg-white/20 dark:bg-black/20
             backdrop-blur-md
             border border-black/10 dark:border-white/10
             rounded-2xl shadow-lg z-50 min-w-[180px]">
                    <li v-for="playlist in playlists" :key="playlist.uuid"
                        @click.stop="!(playlist as any).hasSong && addSongToPlaylist(playlist.uuid)" :class="[
                            'flex items-center gap-3 px-4 py-2 rounded-lg cursor-pointer transition-colors duration-150',
                            (playlist as any).hasSong
                                ? 'text-gray-400 dark:text-gray-500 cursor-not-allowed hover:bg-transparent'
                                : 'hover:bg-white/30 dark:hover:bg-black/30'
                        ]">
                        {{ playlist.name }}
                        <span v-if="(playlist as any).hasSong" class="ml-2 text-xs text-gray-500">✓</span>
                    </li>

                    <li v-if="playlists.length === 0" class="flex items-center gap-3 px-4 py-2 rounded-lg
               cursor-pointer hover:bg-white/30 dark:hover:bg-black/30
               transition-colors duration-150">
                        <Icon name="cancel" class="size-4 text-red-500" />
                        <span class="truncate">{{ $t('songCard.noPlaylistsAvailable') }}</span>
                    </li>
                </ul>
            </li>

            <li @click="addToQueue" class="flex items-center gap-3 px-4 py-2 rounded-lg
           cursor-pointer hover:bg-white/30 dark:hover:bg-black/30
           transition-colors duration-150">
                <Icon name="playlist-add" class="size-5 text-black dark:text-white" />
                <span>{{ $t('songCard.addToQueue') }}</span>
            </li>
        </ul>
    </Teleport>
</template>
