<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import { usePlayerStore } from '@/stores/player'

const player = usePlayerStore()
const audio = ref<HTMLAudioElement | null>(null)

async function playAudio() {
    try { await audio.value?.play() }
    catch (e) { console.warn('Autoplay blocked', e) }
}

function pauseAudio() {
    audio.value?.pause()
}

async function loadTrack() {
    if (!audio.value || !player.audioSrc) return

    const newSrc = new URL(player.audioSrc, location.href).href
    if (audio.value.src !== newSrc) {
        audio.value.src = newSrc
        audio.value.load()
    }

    if (player.isPlaying) await playAudio()
}

watch(() => player.currentTrack, async (track) => {
    if (!audio.value) return
    if (!track) {
        pauseAudio()
        audio.value.src = ''
        return
    }
    await loadTrack()
})

watch(() => player.isPlaying, async (isPlaying) => {
    if (!audio.value || !player.currentTrack) return
    isPlaying ? await playAudio() : pauseAudio()
})

watch(() => player.time, (time) => {
    if (!audio.value || !player.currentTrack) return
    const currentTime = audio.value.currentTime ?? 0
    if (Math.abs(currentTime - time) > 0.5) {
        audio.value.currentTime = time
    }
})

watch(() => player.volume, (v) => {
    if (audio.value) audio.value.volume = v / 100
})

onMounted(() => {
    if (!audio.value) return

    audio.value.volume = player.volume / 100

    audio.value.addEventListener('timeupdate', () => {
        if (!audio.value) return
        player.time = Math.floor(audio.value.currentTime)
    })

    audio.value.addEventListener('loadedmetadata', () => {
        if (!audio.value) return
        player.duration = Math.floor(audio.value.duration)
    })

    audio.value.addEventListener('ended', async () => {
        if (!audio.value) return
        if (player.loopTrack) {
            audio.value.currentTime = 0
            await playAudio()
        } else {
            player.next()
            await loadTrack()
        }
    })
})
</script>

<template>
    <audio ref="audio" preload="metadata" />
    <slot />
</template>