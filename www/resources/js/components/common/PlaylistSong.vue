<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { Icon } from '.'
import { _Song, _Playlist } from '@/types'
import { usePlayerStore } from '@/stores/player'
import { useI18n } from 'vue-i18n'
import { usePage } from '@inertiajs/vue3'

const props = defineProps<{
    index: number
    song: _Song
    playlistUuid: string
}>()

const { t } = useI18n()
const player = usePlayerStore()
const page = usePage()

const dropdownOpen = ref(false)
const menuX = ref(0)
const menuY = ref(0)

const showPlaylists = ref(false)
const playlists = ref<_Playlist[]>([])

function openDropdown(e: MouseEvent) {
    e.preventDefault()

    menuX.value = e.clientX + 6
    menuY.value = e.clientY + 6

    window.dispatchEvent(
        new CustomEvent('song-context-open', { detail: props.song.uuid })
    )

    dropdownOpen.value = true
}

function handleOtherDropdown(e: Event) {
    const event = e as CustomEvent<string>
    if (event.detail !== props.song.uuid) {
        dropdownOpen.value = false
        showPlaylists.value = false
    }
}

function handleClickOutside() {
    dropdownOpen.value = false
    showPlaylists.value = false
}

async function play() {
    const current = player.currentTrack
    player.currentPlaylist = null

    if (!current || current.uuid !== props.song.uuid) {
        await player.playSong(props.song)
    } else {
        await player.togglePlay()
    }
}

function like() {
    player.toggleLike(props.song)
    dropdownOpen.value = false
}

async function addToPlaylist() {
    if (showPlaylists.value) return

    const username = page.props.self.username

    if (player.playlists.size === 0) {
        await player.fetchPlaylists(username)
    }

    const allPlaylists = Array.from(player.playlists.values())

    const checks = await Promise.all(
        allPlaylists.map(async (playlist) => {
            const hasSong =
                playlist.songs?.some(s => s.uuid === props.song.uuid)
                ?? await player.containsSong(playlist, props.song)

                ; (playlist as any).hasSong = hasSong
            return playlist
        })
    )

    playlists.value = checks
    showPlaylists.value = true
}

async function addSongToPlaylist(playlistUuid: string) {
    try {
        await player.addSongToPlaylist(playlistUuid, props.song.uuid)
        dropdownOpen.value = false
        showPlaylists.value = false
    } catch (err) {
        console.error('Failed to add song to playlist', err)
    }
}

function addToQueue() {
    player.addToQueue(props.song)
    dropdownOpen.value = false
}

async function removeSongFromPlaylist() {
    if (!props.song || !props.playlistUuid) return

    const playlist = player.playlists.get(props.playlistUuid)
    if (!playlist) return

    const success = await player.deleteSong(playlist, props.song)
    if (success) {
        playlist.songs = playlist.songs!.filter(s => s.uuid !== props.song.uuid)
        playlist.songs_count = playlist.songs.length
        dropdownOpen.value = false
    }
}

function formatDuration(seconds: number) {
    const m = Math.floor(seconds / 60)
    const s = seconds % 60
    return `${m}:${s.toString().padStart(2, '0')}`
}

function formatAddedDate(dateString: string) {
    const date = new Date(dateString)
    const now = new Date()

    const startOfToday = new Date(now.getFullYear(), now.getMonth(), now.getDate())
    const startOfDate = new Date(date.getFullYear(), date.getMonth(), date.getDate())

    const diffDays = Math.floor(
        (startOfToday.getTime() - startOfDate.getTime()) /
        (1000 * 60 * 60 * 24)
    )

    if (diffDays === 0) return t('songCard.today')
    if (diffDays === 1) return t('songCard.yesterday')
    if (diffDays < 7) return t('songCard.days_ago', { count: diffDays })

    return date.toLocaleDateString(undefined, {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    })
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
    <div @contextmenu.prevent="openDropdown" :class="player.currentTrack?.uuid === props.song.uuid &&
        player.isPlaying &&
        player.currentPlaylist === props.playlistUuid
        ? 'bg-gray-500/10 dark:bg-white/10'
        : ''" class="group grid grid-cols-[32px_48px_1fr_1fr_120px_100px_20px]
        items-center gap-4 px-4 py-2 rounded-md
        text-sm text-neutral-400 hover:bg-gray-500/10 dark:hover:bg-white/10 cursor-pointer">
        <div class="text-right group-hover:hidden">{{ index }}</div>

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
            <div class="truncate">{{ song.artist }}</div>
        </div>

        <div class="min-w-0 truncate">{{ song.album }}</div>

        <div class="justify-self-start tabular-nums">
            {{ formatAddedDate(song.created_at!) }}
        </div>

        <div class="justify-self-end tabular-nums">
            {{ formatDuration(song.duration) }}
        </div>

        <button @click.stop="openDropdown($event)" class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700">
            <Icon name="dots-vertical" class="size-5" />
        </button>
    </div>

    <Teleport to="body">
        <ul v-if="dropdownOpen" :style="{ left: `${menuX}px`, top: `${menuY}px` }" class="fixed text-black dark:text-white bg-white dark:bg-black
            border border-gray-200 dark:border-gray-500/6
            rounded-md shadow-lg py-1 z-50 min-w-[150px]" @click.stop>
            <li @click="like"
                class="flex items-center gap-3 px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer">
                <Icon :name="player.isLiked(song) ? 'heart-off' : 'heart-filled'" :class="player.isLiked(song)
                    ? 'text-black dark:text-white'
                    : 'text-pink-500'" class="size-5" />
                <span>{{ player.isLiked(song) ? t('songCard.unlike') : t('songCard.like') }}</span>
            </li>

            <li class="relative px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">
                <div @mouseenter="addToPlaylist" class="flex items-center gap-3 cursor-pointer">
                    <Icon name="square-rounded-plus" class="size-5" />
                    <span>{{ t('songCard.addToPlaylist') }}</span>
                </div>

                <ul v-if="showPlaylists" class="absolute right-full top-0 bg-white dark:bg-black
                    border border-gray-200 dark:border-gray-500/6
                    rounded-md shadow-lg z-50">
                    <li v-for="playlist in playlists" :key="playlist.uuid"
                        @click.stop="!(playlist as any).hasSong && addSongToPlaylist(playlist.uuid)" :class="[
                            'px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer',
                            (playlist as any).hasSong && 'text-gray-400 cursor-not-allowed hover:bg-transparent'
                        ]">
                        {{ playlist.name }}
                        <span v-if="(playlist as any).hasSong" class="ml-2 text-xs">✓</span>
                    </li>
                </ul>
            </li>

            <li @click="addToQueue"
                class="flex items-center gap-3 px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer">
                <Icon name="playlist-add" class="size-5" />
                <span>{{ t('songCard.addToQueue') }}</span>
            </li>

            <li v-if="props.playlistUuid !== '00000000-0000-0000-0000-000000000000'" @click="removeSongFromPlaylist"
                class="flex items-center gap-3 px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer">
                <Icon name="trash" class="size-5 text-red-500" />
                <span>{{ t('songCard.removeSong') }}</span>
            </li>
        </ul>
    </Teleport>
</template>
