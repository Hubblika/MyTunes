<script setup lang="ts">
import { onMounted, watch, computed, ref } from 'vue'
import { Button, Icon, Slider } from '@/components/common'
import { usePlayerStore } from '@/stores/player'
import { router } from '@inertiajs/vue3'

const player = usePlayerStore()
const audio = ref<HTMLAudioElement | null>(null)
const hasTrack = computed(() => !!player.currentTrack)

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
        try { await audio.value.play() }
        catch (e) { console.warn('Autoplay blocked', e) }
    }
}

function togglePlay() { player.togglePlay() }
function skipForward() { player.next(); loadTrack() }
function skipBack() { player.previous(); loadTrack() }

function mute() {
    player.volume = player.volume === 0 ? 10 : 0
}


function openQueue() { router.get('/queue') }

const formattedTime = (time: number) => {
    const minutes = Math.floor(time / 60)
    const seconds = time % 60
    return `${minutes}:${seconds.toString().padStart(2, '0')}`
}

watch(() => player.currentTrack, async (newTrack) => {
    if (!audio.value) return
    if (!newTrack) {
        audio.value.pause()
        audio.value.src = ''
        return
    }

    const newSrc = new URL(player.audioSrc, location.href).href
    if (audio.value.src !== newSrc) {
        audio.value.src = newSrc
        audio.value.load()
    }

    if (player.isPlaying) {
        try { await audio.value.play() }
        catch (e) { console.warn('Autoplay blocked', e) }
    }
})

watch(() => player.isPlaying, async (isPlaying) => {
    if (!audio.value || !player.currentTrack) return
    if (isPlaying) {
        try { await audio.value.play() }
        catch (e) { console.warn('Autoplay blocked', e) }
    } else {
        audio.value.pause()
    }
})

watch(() => player.time, (newTime) => {
    if (!audio.value) return
    if (Math.abs(audio.value.currentTime - newTime) > 0.5) {
        audio.value.currentTime = newTime
    }
})

watch(() => player.volume, (v) => {
    if (audio.value) audio.value.volume = v / 100
})

onMounted(() => {
    if (!audio.value) return
    audio.value.volume = player.volume / 100

    audio.value.addEventListener('timeupdate', () => {
        player.time = Math.floor(audio.value!.currentTime)
    })

    audio.value.addEventListener('loadedmetadata', () => {
        player.duration = Math.floor(audio.value!.duration)
    })

    audio.value.addEventListener('ended', async () => {
        if (player.loopTrack) {
            audio.value!.currentTime = 0
            await audio.value!.play()
        } else {
            player.next()
            loadTrack()
        }
    })
})
</script>

<template>
    <audio ref="audio" preload="metadata"></audio>
    <div class="fixed bottom-6 md:bottom-8 left-1/2 -translate-x-1/2 
            w-[calc(100%-2rem)] max-w-7xl z-40">
        <div class="grid grid-cols-[1fr_auto_1fr] items-center
           p-4
           rounded-2xl
           border border-black/10 dark:border-white/10
           bg-white/20 dark:bg-white/5
           backdrop-blur-xl
           shadow-xl dark:shadow-2xl
           transition-all duration-300">

            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gray-700 rounded">
                    <img :src="player.currentTrack?.cover_url ?? '/uploads/thumbnails/defaultThumbnail.png'"
                        alt="Album Art" class="w-full h-full object-cover rounded" />
                </div>
                <div>
                    <p
                        :class="!hasTrack ? 'text-gray-400 dark:text-gray-600 font-semibold text-sm' : 'hover:underline hover:cursor-pointer font-semibold text-sm'">
                        {{ player.currentTrack?.title ?? $t('toolbar.songTitle') }}
                    </p>
                    <p
                        :class="!hasTrack ? 'text-gray-400 dark:text-gray-600 text-xs' : 'hover:underline hover:cursor-pointer text-xs text-gray-600 dark:text-gray-300'">
                        {{ player.currentTrack?.artist ?? $t('toolbar.artistName') }}
                    </p>
                </div>
                <Button @click="player.toggleLike()" :disabled="!hasTrack" :tooltip="player.isLiked(player.currentTrack) ? $t('tooltip.unlike') : $t('tooltip.like')"
                    :class="['group transition-all duration-150', !hasTrack ? 'cursor-not-allowed opacity-50' : '']">
                    <Icon :name="player.isLiked(player.currentTrack) ? 'heart-filled' : 'heart'"
                        :class="['size-6 transition-colors duration-150', player.isLiked(player.currentTrack) ? 'text-pink-500 group-hover:text-pink-400' : 'text-black dark:text-white group-hover:text-black/60 dark:group-hover:text-white/80']" />
                </Button>
            </div>

            <div class="flex flex-col items-center w-2xl space-y-1">

                <div class="flex items-center gap-4">
                    <Button @click="player.shuffle ? player.sortQueue() : player.shuffleQueue()" :disabled="!hasTrack" :tooltip="player.shuffle ? $t('tooltip.disableShuffle') : $t('tooltip.enableShuffle')"
                        :class="['group transition-all duration-150', !hasTrack ? 'cursor-not-allowed opacity-50' : '', player.shuffle ? 'text-cyan-500 group-hover:text-cyan-300' : '']">
                        <Icon name="arrows-shuffle" class="size-5 transition-colors duration-150" />
                    </Button>

                    <Button @click="skipBack" :disabled="!hasTrack" :tooltip="$t('tooltip.previous')"
                        :class="['group transition-all duration-150', !hasTrack ? 'cursor-not-allowed opacity-50' : '']">
                        <Icon name="skip-back" class="size-6 transition-colors duration-150" />
                    </Button>

                    <Button @click="togglePlay" :disabled="!hasTrack" :tooltip="player.isPlaying ? $t('tooltip.pause') : $t('tooltip.play')"
                        :class="['w-12 h-12 rounded-full flex items-center justify-center transition-all duration-150', !hasTrack ? 'bg-gray-300 dark:bg-gray-700 cursor-not-allowed' : 'bg-cyan-500 hover:bg-cyan-400']">
                        <Icon :name="player.isPlaying ? 'player-pause-filled' : 'player-play-filled'"
                            class="size-6 text-white" />
                    </Button>

                    <Button @click="skipForward" :disabled="!hasTrack" :tooltip="$t('tooltip.next')"
                        :class="['group transition-all duration-150', !hasTrack ? 'cursor-not-allowed opacity-50' : '']">
                        <Icon name="skip-forward" class="size-6 transition-colors duration-150" />
                    </Button>

                    <Button @click="player.loop = !player.loop" :disabled="!hasTrack" :tooltip="player.loop ? $t('tooltip.disableLoop') : $t('tooltip.enableLoop')"
                        :class="['group transition-all duration-150', !hasTrack ? 'cursor-not-allowed opacity-50' : '', player.loop ? 'text-cyan-500 group-hover:text-cyan-300' : '']">
                        <Icon name="repeat" class="size-5 transition-colors duration-150" />
                    </Button>
                </div>

                <div class="flex items-center gap-2 w-full text-xs text-gray-600 dark:text-gray-300">
                    <span :class="!hasTrack ? 'text-gray-400 dark:text-gray-600' : ''">
                        {{ formattedTime(player.time) }}
                    </span>
                    <Slider :class="!hasTrack ? 'opacity-50' : ''" v-model="player.time" :max="player.duration"
                        :disabled="!hasTrack"></Slider>
                    <span :class="!hasTrack ? 'text-gray-400 dark:text-gray-600' : ''">
                        {{ formattedTime(player.duration) }}
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-2 justify-self-end">
                <Button @click="openQueue" :disabled="!hasTrack" :tooltip="$t('tooltip.queue')"
                    :class="['px-2! group transition-all duration-150', !hasTrack ? 'cursor-not-allowed opacity-50' : '']">
                    <Icon name="list" class="size-5 transition-colors duration-150" />
                </Button>

                <div class="flex items-center gap-2">
                    <Button @click="mute" :disabled="!hasTrack" :tooltip="player.volume > 0 ? $t('tooltip.mute') : $t('tooltip.unmute')"
                        :class="['px-2! group transition-all duration-150', !hasTrack ? 'cursor-not-allowed opacity-50' : '']">
                        <Icon :name="volumeIcon" class="size-5 transition-colors duration-150" />
                    </Button>
                    <Slider :class="!hasTrack ? 'opacity-50' : ''" v-model="player.volume" :max="100"
                        :disabled="!hasTrack">
                    </Slider>
                </div>
            </div>
        </div>
    </div>
</template>
