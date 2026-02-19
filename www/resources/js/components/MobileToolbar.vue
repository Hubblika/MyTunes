<script setup lang="ts">
import { ref, computed } from 'vue'
import { Button, Icon } from '@/components/common'
import { usePlayerStore } from '@/stores/player'

const player = usePlayerStore()

const hasTrack = computed(() => !!player.currentTrack)

const touchStartX = ref<number | null>(null)

const onTouchStart = (e: TouchEvent) => {
    touchStartX.value = e.touches[0].clientX
}

const onTouchEnd = (e: TouchEvent) => {
    if (touchStartX.value === null) return

    const deltaX = e.changedTouches[0].clientX - touchStartX.value

    if (Math.abs(deltaX) > 50) {
        if (deltaX < 0) {
            player.next()
        } else {
            player.previous()
        }
    }

    touchStartX.value = null
}
</script>

<template>
    <div class="fixed bottom-16 left-1/2 -translate-x-1/2 z-50 w-[95%] max-w-md" @touchstart="onTouchStart"
        @touchend="onTouchEnd">
        <div class="grid grid-cols-[1fr_auto_1fr] items-center
             p-3
             rounded-4xl
             border border-black/10 dark:border-white/10
             bg-white/20 dark:bg-white/5
             backdrop-blur-xl
             shadow-xl dark:shadow-2xl
             transition-all duration-300">
            <!-- Track info -->
            <div class="flex items-center gap-2 min-w-0">
                <img :src="player.currentTrack?.cover_url ?? '/uploads/thumbnails/defaultThumbnail.png'"
                    class="w-8 h-8 rounded-xl object-cover" />

                <div class="min-w-0">
                    <p class="text-sm font-semibold truncate">
                        {{ player.currentTrack?.title ?? $t('toolbar.songTitle') }}
                    </p>
                    <p class="text-xs text-gray-500 truncate">
                        {{ player.currentTrack?.artist ?? $t('toolbar.artistName') }}
                    </p>
                </div>
            </div>

            <div></div>

            <!-- Buttons -->
            <div class="flex justify-end items-center gap-2">
                <Button @click="player.toggleLike()" :disabled="!hasTrack"
                    class="w-10 h-10 rounded-full flex items-center justify-center active:scale-90 transition">
                    <Icon :name="player.isLiked(player.currentTrack) ? 'heart-filled' : 'heart'"
                        :class="['size-5 transition-colors duration-150', player.isLiked(player.currentTrack) ? 'text-pink-500 group-hover:text-pink-400' : 'text-black dark:text-white group-hover:text-black/60 dark:group-hover:text-white/80']" />
                </Button>

                <Button @click="player.togglePlay" :disabled="!hasTrack"
                    class="w-10 h-10 rounded-full flex items-center justify-center text-white active:scale-90 transition">
                    <Icon :name="player.isPlaying ? 'player-pause-filled' : 'player-play-filled'" />
                </Button>
            </div>
        </div>
    </div>
</template>
