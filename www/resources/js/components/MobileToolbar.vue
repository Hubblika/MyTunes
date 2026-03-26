<script setup lang="ts">
import { ref, computed, onMounted, nextTick } from 'vue';
import { Button, Icon } from '@/components/common';
import { usePlayerStore } from '@/stores/player';

const player = usePlayerStore();
const hasTrack = computed(() => !!player.currentTrack);

const touchStartX = ref<number | null>(null);

const onTouchStart = (e: TouchEvent) => {
    touchStartX.value = e.touches[0].clientX;
}

const onTouchEnd = (e: TouchEvent) => {
    if (touchStartX.value === null) return;

    const deltaX = e.changedTouches[0].clientX - touchStartX.value;

    if (Math.abs(deltaX) > 50) {
        if (deltaX < 0) {
            player.next();
        } else {
            player.previous();
        }
    }

    touchStartX.value = null;
}

const titleRef = ref<HTMLParagraphElement | null>(null);
const artistRef = ref<HTMLParagraphElement | null>(null);

const scrollText = (el: HTMLParagraphElement | null) => {
    if (!el) return;

    const scrollWidth = el.scrollWidth;
    const clientWidth = el.clientWidth;

    if (scrollWidth <= clientWidth) return;

    let scrollPos = 0;

    const animate = () => {
        scrollPos += 1;
        if (scrollPos > scrollWidth) scrollPos = -clientWidth;
        el.scrollLeft = scrollPos;
        requestAnimationFrame(animate);
    }

    animate();
}

onMounted(() => {
    nextTick(() => {
        scrollText(titleRef.value);
        scrollText(artistRef.value);
    });
});
</script>

<template>
    <div class="fixed bottom-16 left-1/2 -translate-x-1/2 z-50 w-[95%] max-w-md"
        @touchstart="onTouchStart" @touchend="onTouchEnd">
        <div class="grid grid-cols-[1fr_auto_1fr] items-center
             p-3
             rounded-4xl
             border border-black/10 dark:border-white/10
             bg-white/20 dark:bg-white/5
             backdrop-blur-xl
             shadow-xl dark:shadow-2xl
             transition-all duration-300">

            <div class="flex items-center gap-3 min-w-0">
                <img :src="player.currentTrack?.cover_url ?? '/uploads/thumbnails/defaultThumbnail.png'"
                    class="w-10 h-10 rounded-xl object-cover" />

                <div class="min-w-0 flex flex-col overflow-hidden">
                    <p ref="titleRef" class="text-sm font-semibold truncate whitespace-nowrap">
                        {{ player.currentTrack?.title ?? $t('toolbar.songTitle') }}
                    </p>

                    <p ref="artistRef" class="text-xs text-gray-500 truncate whitespace-nowrap">
                        {{ player.currentTrack?.artist ?? $t('toolbar.artistName') }}
                    </p>
                </div>
            </div>

            <div></div>

            <div class="flex justify-end items-center gap-3">

                <Button @click.stop="player.toggleLike()" :disabled="!hasTrack"
                    class="w-12 h-12 rounded-full flex items-center justify-center active:scale-90 transition">
                    <Icon :name="player.isLiked(player.currentTrack) ? 'heart-filled' : 'heart'" :class="[
                        'size-6 transition-colors duration-150',
                        player.isLiked(player.currentTrack)
                            ? 'text-pink-500'
                            : 'text-black dark:text-white'
                    ]" />
                </Button>

                <Button @click.stop="player.togglePlay" :disabled="!hasTrack"
                    class="w-14 h-14 rounded-full flex items-center justify-center text-black dark:text-white active:scale-90 transition">
                    <Icon :name="player.isPlaying ? 'player-pause-filled' : 'player-play-filled'" class="size-7" />
                </Button>

            </div>
        </div>
    </div>
</template>