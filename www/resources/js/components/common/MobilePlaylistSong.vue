<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount, nextTick } from 'vue'
import { Icon, Button } from '.'
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

const dropdownRef = ref<HTMLElement | null>(null)
const dropdownOpen = ref(false)
const menuX = ref(0)
const menuY = ref(0)
const OFFSET = 6

const showPlaylists = ref(false)
const playlists = ref<_Playlist[]>([])

const isMobile = ref(false)

function checkMobile() {
    isMobile.value = window.innerWidth <= 768
}

function openDropdown(e: MouseEvent) {
    e.preventDefault()
    e.stopPropagation()

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

function handleOtherDropdown(e: Event) {
    const event = e as CustomEvent<string>
    if (event.detail !== props.song.uuid) {
        dropdownOpen.value = false
        showPlaylists.value = false
    }
}

function handleClickOutside(e: MouseEvent) {
    const target = e.target as Node
    const isOutsideDesktop = dropdownRef.value && !dropdownRef.value.contains(target)

    const mobileDropdown = document.querySelector('#mobile-dropdown')
    const isOutsideMobile = mobileDropdown && !mobileDropdown.contains(target)

    if (isOutsideDesktop && isOutsideMobile) {
        dropdownOpen.value = false
        showPlaylists.value = false
    }
}

async function play() {
    const current = player.currentTrack
    player.currentPlaylist = props.playlistUuid
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
    if (!showPlaylists.value) {
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
    }

    showPlaylists.value = !showPlaylists.value
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

onMounted(() => {
    window.addEventListener('song-context-open', handleOtherDropdown)
    window.addEventListener('click', handleClickOutside)
    checkMobile()
    window.addEventListener('resize', checkMobile)
})

onBeforeUnmount(() => {
    window.removeEventListener('song-context-open', handleOtherDropdown)
    window.removeEventListener('click', handleClickOutside)
    window.removeEventListener('resize', checkMobile)
})
</script>

<template>
    <div @click.stop="play" @contextmenu.prevent="openDropdown" :class="player.currentTrack?.uuid === props.song.uuid &&
        player.isPlaying &&
        player.currentPlaylist === props.playlistUuid
        ? 'bg-gray-500/10 dark:bg-white/10'
        : ''" class="group flex items-center gap-3 px-4 py-2 rounded-md
            text-sm text-neutral-400 hover:bg-gray-500/10 dark:hover:bg-white/10
            cursor-pointer">

        <div class="w-6 flex justify-end shrink-0 text-black dark:text-white">
            {{ index }}
        </div>

        <img :src="song.cover_url" alt="cover" class="w-10 h-10 rounded object-cover shrink-0" />

        <div class="min-w-0 flex-1">
            <div class="text-black dark:text-white font-medium truncate">
                {{ song.title }}
            </div>
            <div class="truncate">
                {{ song.artist }}
            </div>
        </div>

        <Button @click.stop="openDropdown($event)" :tooltip="$t('tooltip.moreOptions')" class="flex justify-center items-center w-9 h-9 p-0 rounded-full
                   opacity-70 group-hover:opacity-100 transition-opacity shrink-0">
            <Icon name="dots-vertical"
                class="text-black dark:text-white size-6 transition-transform duration-150 group-hover:scale-110" />
        </Button>
    </div>

    <Teleport to="body">
        <ul ref="dropdownRef" v-if="dropdownOpen && !isMobile" :style="{ left: `${menuX}px`, top: `${menuY}px` }" class="fixed text-black dark:text-white
           bg-white/20 dark:bg-black/20
           backdrop-blur-md
           border border-black/10 dark:border-white/10
           rounded-2xl shadow-lg py-2 z-50
           min-w-[220px] whitespace-nowrap" @click.stop>
            <li @click="like" class="flex items-center gap-3 px-4 py-2 rounded-lg
             cursor-pointer hover:bg-white/30 dark:hover:bg-black/30 transition-colors duration-150">
                <Icon :name="player.isLiked(song) ? 'heart-off' : 'heart-filled'"
                    :class="player.isLiked(song) ? 'text-black dark:text-white' : 'text-pink-500'" class="size-5" />
                <span>{{ player.isLiked(song) ? t('songCard.unlike') : t('songCard.like') }}</span>
            </li>

            <li class="relative flex items-center gap-3 px-4 py-2 rounded-lg cursor-pointer
             hover:bg-white/30 dark:hover:bg-black/30 transition-colors duration-150">
                <div @mouseenter="addToPlaylist" class="flex items-center gap-3 w-full">
                    <Icon name="square-rounded-plus" class="size-5" />
                    <span>{{ t('songCard.addToPlaylist') }}</span>
                </div>

                <ul v-if="showPlaylists" class="absolute top-0 right-full translate-x-1
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
                        <span v-if="(playlist as any).hasSong" class="ml-2 text-xs">✓</span>
                    </li>
                </ul>
            </li>

            <li @click="addToQueue" class="flex items-center gap-3 px-4 py-2 rounded-lg
             cursor-pointer hover:bg-white/30 dark:hover:bg-black/30 transition-colors duration-150">
                <Icon name="playlist-add" class="size-5" />
                <span>{{ t('songCard.addToQueue') }}</span>
            </li>

            <li v-if="props.playlistUuid !== '00000000-0000-0000-0000-000000000000'" @click="removeSongFromPlaylist"
                class="flex items-center gap-3 px-4 py-2 rounded-lg
             cursor-pointer hover:bg-white/30 dark:hover:bg-black/30 transition-colors duration-150">
                <Icon name="trash" class="size-5 text-red-500" />
                <span>{{ t('songCard.removeSong') }}</span>
            </li>
        </ul>
    </Teleport>

    <Teleport to="body">
        <transition name="slide-up">
            <div id="mobile-dropdown" v-if="dropdownOpen && isMobile" @click.self="dropdownOpen = false"
                class="fixed inset-0 bg-black/40 z-50 flex justify-center items-end">

                <div class="bg-white/20 dark:bg-black/20 backdrop-blur-xl border border-white/10 dark:border-black/10
          rounded-t-3xl w-full max-h-[80vh] overflow-auto p-4">

                    <li @click="like"
                        class="flex items-center gap-3 px-4 py-2 rounded-lg cursor-pointer hover:bg-white/30 dark:hover:bg-black/30 transition">
                        <Icon :name="player.isLiked(props.song) ? 'heart-off' : 'heart-filled'"
                            :class="player.isLiked(props.song) ? 'text-black dark:text-white' : 'text-pink-500'"
                            class="size-5" />
                        <span>{{ player.isLiked(props.song) ? $t('songCard.unlike') : $t('songCard.like') }}</span>
                    </li>

                    <li class="flex items-center gap-3 px-4 py-2 rounded-lg cursor-pointer hover:bg-white/30 dark:hover:bg-black/30 transition"
                        @click="addToPlaylist">
                        <Icon name="square-rounded-plus" class="size-5" />
                        <span>{{ $t('songCard.addToPlaylist') }}</span>
                    </li>

                    <li @click="addToQueue"
                        class="flex items-center gap-3 px-4 py-2 rounded-lg cursor-pointer hover:bg-white/30 dark:hover:bg-black/30 transition">
                        <Icon name="playlist-add" class="size-5" />
                        <span>{{ $t('songCard.addToQueue') }}</span>
                    </li>

                    <li v-if="props.playlistUuid !== '00000000-0000-0000-0000-000000000000'"
                        @click="removeSongFromPlaylist"
                        class="flex items-center gap-3 px-4 py-2 rounded-lg cursor-pointer hover:bg-white/30 dark:hover:bg-black/30 transition">
                        <Icon name="trash" class="size-5 text-red-500" />
                        <span>{{ $t('songCard.removeSong') }}</span>
                    </li>

                    <ul v-if="showPlaylists"
                        class="mt-2 bg-white/20 dark:bg-black/20 backdrop-blur-xl border border-white/10 dark:border-black/10 rounded-2xl shadow-inner overflow-auto max-h-64">
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
                    </ul>

                </div>
            </div>
        </transition>
    </Teleport>
</template>