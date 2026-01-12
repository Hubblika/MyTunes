<script setup lang="ts">
import { onMounted, watch, nextTick, computed, ref } from 'vue'
import { Button, Icon, Slider } from '@/components/common'
import { usePlayerStore } from '@/stores/player'

const player = usePlayerStore()
const audio = ref<HTMLAudioElement | null>(null)
const liked = ref(false);

const volumeIcon = computed(() => {
    if (player.volume === 0) return 'volume-3'
    if (player.volume < 50) return 'volume-2'
    return 'volume'
})

async function loadTrack() {
    if (!audio.value || !player.audioSrc) return
    audio.value.src = player.audioSrc
    audio.value.load()
    if (player.isPlaying) {
        try {
            await audio.value.play()
        } catch (e) {
            console.warn('Autoplay blocked', e)
        }
    }
}

function togglePlay() {
    player.togglePlay()
    loadTrack()
    if (!audio.value) return
    if (player.isPlaying) {
        audio.value.play()
    } else {
        audio.value.pause()
    }
}

function skipForward() {
    player.next()
    loadTrack()
}

function skipBack() {
    player.previous()
    loadTrack()
}

function mute() {
    if (player.volume === 0) player.volume = 10
    else player.volume = 0
}

function like() {
    //TODO: implement like functionality
    liked.value = !liked.value;
}

function openLyrics() {
    //TODO: implement open lyrics functionality
}

function openQueue() {
    //TODO: implement open queue functionality
}


watch(
    () => player.volume,
    (v) => {
        if (audio.value) audio.value.volume = v / 100
    }
)

watch(
    () => player.time,
    (newTime) => {
        if (!audio.value) return
        if (Math.abs(audio.value.currentTime - newTime) > 0.5) {
            audio.value.currentTime = newTime
        }
    }
)

watch(
    () => player.isPlaying,
    (isPlaying) => {
        if (!isPlaying) {
            if (!audio.value) return
            audio.value.pause()
            return
        } else {
            loadTrack()

            if (!audio.value) return
            audio.value.play()
        }
    }
)

onMounted(() => {
    if (!audio.value) return
    audio.value.volume = player.volume / 100

    audio.value.addEventListener('timeupdate', () => {
        player.time = Math.floor(audio.value!.currentTime)
    })

    audio.value.addEventListener('loadedmetadata', () => {
        player.duration = Math.floor(audio.value!.duration)
    })

    audio.value.addEventListener('ended', () => {
        skipForward()
    })

    loadTrack()
})
</script>

<template>
    <audio ref="audio"></audio>
    <div
        class="flex-none left-0 w-full bg-white dark:bg-black backdrop-blur-md text-black dark:text-white p-3 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-gray-700 rounded">
                <img :src="player.currentTrack?.cover_url ?? '/uploads/thumbnails/playlist/defaultThumbnail.png'"
                    alt="Album Art" class="w-full h-full object-cover rounded" />
            </div>
            <div>
                <p class="font-semibold text-sm hover:underline hover:cursor-pointer">{{ player.currentTrack?.title ??
                    $t('toolbar.songTitle') }}
                </p>
                <p class="text-xs text-gray-600 dark:text-gray-300 hover:underline hover:cursor-pointer">{{
                    player.currentTrack?.artist ?? $t('toolbar.artistName') }}</p>
            </div>
            <Button @click="like" class="group transition-all duration-150">
                <Icon :name="liked ? 'heart-filled' : 'heart'"
                    :class="['size-6 transition-colors duration-150', liked ? 'text-pink-500 group-hover:text-pink-400' : 'text-black dark:text-white group-hover:text-black/60 dark:group-hover:text-white/80']" />
            </Button>
        </div>

        <div class="flex flex-col items-center w-2xl space-y-1">
            <div class="flex items-center gap-4">
                <Button @click="player.shuffle = !player.shuffle" class="group transition-all duration-150">
                    <Icon name="arrows-shuffle"
                        class="size-5 transition-colors duration-150 group-hover:text-black/60 dark:group-hover:text-white/80"
                        :class="player.shuffle ? 'text-cyan-500 group-hover:text-cyan-300' : ''" />
                </Button>
                <Button @click="skipBack" class="group transition-all duration-150">
                    <Icon name="skip-back"
                        class="size-6 transition-colors duration-150 group-hover:text-black/60 dark:group-hover:text-white/80" />
                </Button>
                <Button @click="togglePlay"
                    class="w-12 h-12 rounded-full bg-cyan-500 flex items-center justify-center transition-all duration-150 hover:bg-cyan-400">
                    <Icon :name="player.isPlaying ? 'player-pause-filled' : 'player-play-filled'"
                        class="size-6 text-white" />
                </Button>
                <Button @click="skipForward" class="group transition-all duration-150">
                    <Icon name="skip-forward"
                        class="size-6 transition-colors duration-150 group-hover:text-black/60 dark:group-hover:text-white/80" />
                </Button>
                <Button @click="player.loop = !player.loop" class="group transition-all duration-150">
                    <Icon name="repeat"
                        class="size-5 transition-colors duration-150 group-hover:text-black/60 dark:group-hover:text-white/80"
                        :class="player.loop ? 'text-cyan-500 group-hover:text-cyan-300' : ''" />
                </Button>
            </div>

            <div class="flex items-center gap-2 w-full text-xs text-gray-600 dark:text-gray-300">
                <span class="hover:cursor-default">{{ player.time }}</span>
                <Slider v-model="player.time" :max="player.duration"></Slider>
                <span class="hover:cursor-default">{{ player.duration }}</span>
            </div>
        </div>

        <div class="flex items-center">
            <Button @click="openLyrics" class="px-2! group transition-all duration-150">
                <Icon name="microphone-2"
                    class="size-5 transition-colors duration-150 group-hover:text-black/60 dark:group-hover:text-white/80" />
            </Button>
            <Button @click="openQueue" class="px-2! group transition-all duration-150">
                <Icon name="list"
                    class="size-5 transition-colors duration-150 group-hover:text-black/60 dark:group-hover:text-white/80" />
            </Button>
            <div class="flex items-center gap-2">
                <Button @click="mute" class="px-2! group transition-all duration-150">
                    <Icon :name="volumeIcon"
                        class="size-5 transition-colors duration-150 group-hover:text-black/60 dark:group-hover:text-white/80" />
                </Button>
                <Slider v-model="player.volume" :max="100"></Slider>
            </div>
        </div>
    </div>
</template>
