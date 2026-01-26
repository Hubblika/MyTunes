<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount, watch } from 'vue'
import { Icon } from '.'
import { usePlayerStore } from '@/stores/player'
import { _Song } from '@/types'

const props = defineProps<{
    song: _Song
}>()

const player = usePlayerStore()

const dropdownOpen = ref(false)
const menuX = ref(0)
const menuY = ref(0)

async function play() {
    const current = player.currentTrack
    player.currentPlaylist = null

    if (!current || current.uuid !== props.song.uuid) {
        await player.playSong(props.song)
    } else {
        await player.togglePlay()
    }
}

function openDropdown(e: MouseEvent) {
    menuX.value = e.clientX + 6
    menuY.value = e.clientY + 6

    window.dispatchEvent(
        new CustomEvent('song-context-open', {
            detail: props.song.uuid
        })
    )

    dropdownOpen.value = true
}


function like() {
    player.toggleLike(props.song)
    dropdownOpen.value = false;
}

function addToPlaylist() {

    dropdownOpen.value = false;
}

function addToQueue() {
    player.addToQueue(props.song)
    dropdownOpen.value = false;
}

function handleOtherDropdown(e: Event) {
    const event = e as CustomEvent<string>
    if (event.detail !== props.song.uuid) {
        dropdownOpen.value = false
    }
}

function handleClickOutside() {
    dropdownOpen.value = false
}

onMounted(async () => {
    window.addEventListener('song-context-open', handleOtherDropdown)
    window.addEventListener('click', handleClickOutside)
})

onBeforeUnmount(() => {
    window.removeEventListener('song-context-open', handleOtherDropdown)
    window.removeEventListener('click', handleClickOutside)
})
</script>

<template>
        <div class="relative group w-44 cursor-pointer rounded-lg
                p-3
                transition-colors duration-200
                hover:bg-gray-500/10 dark:hover:bg-white/10" :class="player.currentTrack?.uuid === props.song.uuid && player.isPlaying
                    ? 'bg-gray-500/10 dark:bg-white/10'
                    : ''" @contextmenu.prevent="openDropdown">
        <div class="relative mb-3 aspect-square overflow-hidden rounded-md">
            <img :src="props.song.cover_url ?? '/uploads/thumbnails/defaultThumbnail.png'" alt="" class="h-full w-full object-cover
                       transition-transform duration-300
                       group-hover:scale-105" />

            <button @click.stop="play" class="absolute bottom-2 right-2
                       flex h-10 w-10 items-center justify-center
                       rounded-full bg-cyan-500 text-white shadow-lg

                       opacity-0 translate-y-3 scale-90
                       transition-all duration-300 ease-out delay-75

                       group-hover:opacity-100
                       group-hover:translate-y-0
                       group-hover:scale-100

                       hover:scale-105 active:scale-95">
                <Icon :name="player.currentTrack?.uuid === props.song.uuid && player.isPlaying
                    ? 'player-pause-filled'
                    : 'player-play-filled'" class="size-6 text-white" />
            </button>
        </div>

        <h3 class="truncate text-sm font-semibold text-black dark:text-white text-center">
            {{ props.song.title }}
        </h3>
    </div>

    <ul v-if="dropdownOpen" class="fixed bg-white dark:bg-black
               border border-gray-200 dark:border-gray-500/6
               rounded-md shadow-lg py-1 z-50" :style="{ left: `${menuX}px`, top: `${menuY}px` }" @click.stop>
        <li @click="() => like()" class="flex items-center gap-3 px-4 py-2
                   hover:bg-gray-100 dark:hover:bg-gray-700
                   cursor-pointer whitespace-nowrap">
            <Icon :name="player.isLiked(props.song) ? 'heart-off' : 'heart-filled'" :class="player.isLiked(props.song) ? 'text-black dark:text-white' : 'text-pink-500 dark:text-pink-500'" class="size-5" />
            <span>{{ player.isLiked(props.song) ? $t('songCard.unlike') : $t('songCard.like') }}</span>
        </li>

        <li @click="() => addToPlaylist()" class="flex items-center gap-3 px-4 py-2
                   hover:bg-gray-100 dark:hover:bg-gray-700
                   cursor-pointer whitespace-nowrap">
            <Icon name="square-rounded-plus" class="size-5 text-black dark:text-white" />
            <span>{{ $t('songCard.addToPlaylist') }}</span>
        </li>

        <li @click="() => addToQueue()" class="flex items-center gap-3 px-4 py-2
                   hover:bg-gray-100 dark:hover:bg-gray-700
                   cursor-pointer whitespace-nowrap">
            <Icon name="playlist-add" class="size-5 text-black dark:text-white" />
            <span>{{ $t('songCard.addToQueue') }}</span>
        </li>
    </ul>
</template>
