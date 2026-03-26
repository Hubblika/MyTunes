<script setup lang="ts">
import { Button, Icon, Slider } from '@/components/common';
import { usePlayerStore } from '@/stores/player';
import { router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const player = usePlayerStore();
const isVisible = ref(false);
const hasTrack = computed(() => !!player.currentTrack);

const startY = ref<number | null>(null);

function onTouchStart(e: TouchEvent) {
    startY.value = e.touches[0].clientY;
}

function onTouchEnd(e: TouchEvent) {
    if (startY.value === null) return;
    const deltaY = e.changedTouches[0].clientY - startY.value;
    if (deltaY > 80) close();
    startY.value = null;
}

function openQueue() {
        isVisible.value = false;
    router.get('/queue');
}

function open() {
    if (hasTrack.value) isVisible.value = true;
}

function close() {
    isVisible.value = false;
}

const formattedTime = (time: number) => {
    const m = Math.floor(time / 60);
    const s = time % 60;
    return `${m}:${s.toString().padStart(2, '0')}`;
}

defineExpose({ open });
</script>

<template>
    <transition name="fade">
        <div v-if="isVisible" class="fixed inset-0 z-999 bg-black/50 flex flex-col" @touchstart="onTouchStart"
            @touchend="onTouchEnd">

            <div class="flex-1 flex flex-col items-center justify-center px-4 pb-10">
                <div class="w-full max-w-md grid grid-cols-[1fr_auto_1fr] items-center
                    p-4 rounded-2xl
                    border border-black/10 dark:border-white/10
                    bg-white/20 dark:bg-white/5
                    backdrop-blur-xl
                    shadow-xl dark:shadow-2xl
                    transition-all duration-300">
                    <div class="flex flex-col items-center text-center col-start-2">
                        <div class="flex items-center justify-between p-4">
                            <div class="w-10" />
                            <p class="text-sm font-semibold opacity-70">{{ $t('toolbar.nowPlaying') }}</p>
                            <Button @click="close" class="w-10 h-10 flex items-center justify-center">
                                <Icon name="chevron-down" class="size-6" />
                            </Button>
                        </div>
                        <img :src="player.currentTrack?.cover_url ?? '/uploads/thumbnails/defaultThumbnail.png'"
                            class="w-64 h-64 rounded-2xl shadow-xl object-cover mb-6" />

                        <p class="text-lg font-bold truncate max-w-[220px]">
                            {{ player.currentTrack?.title ?? 'No track' }}
                        </p>
                        <p class="text-sm opacity-70 truncate max-w-[220px] mb-6">
                            {{ player.currentTrack?.artist ?? '' }}
                        </p>

                        <div class="w-full mb-2">
                            <Slider v-model="player.time" :max="player.duration" :disabled="!hasTrack" />
                        </div>

                        <div class="w-full flex justify-between text-xs opacity-70 mb-6">
                            <span>{{ formattedTime(player.time) }}</span>
                            <span>{{ formattedTime(player.duration) }}</span>
                        </div>

                        <div class="flex items-center justify-center gap-8 mb-6">
                            <Button @click="player.previous()" :disabled="!hasTrack">
                                <Icon name="skip-back" class="size-8" />
                            </Button>

                            <Button @click="player.togglePlay()" :disabled="!hasTrack"
                                class="w-16 h-16 rounded-full bg-cyan-500 hover:bg-cyan-400 flex items-center justify-center">
                                <Icon :name="player.isPlaying ? 'player-pause-filled' : 'player-play-filled'"
                                    class="size-8 text-white" />
                            </Button>

                            <Button @click="player.next()" :disabled="!hasTrack">
                                <Icon name="skip-forward" class="size-8" />
                            </Button>
                        </div>

                        <div class="flex items-center justify-center gap-6 opacity-80">
                            <Button @click="player.shuffleQueue()" :disabled="!hasTrack">
                                <Icon name="arrows-shuffle" class="size-6" />
                            </Button>

                            <Button @click="player.loop = !player.loop" :disabled="!hasTrack">
                                <Icon name="repeat" class="size-6" />
                            </Button>

                            <Button @click="player.toggleLike()" :disabled="!hasTrack">
                                <Icon :name="player.isLiked(player.currentTrack) ? 'heart-filled' : 'heart'"
                                    class="size-6"
                                    :class="player.isLiked(player.currentTrack) ? 'text-pink-500' : ''" />
                            </Button>

                            <Button @click="openQueue()" :disabled="!hasTrack">
                                <Icon name="list" class="size-6" />
                            </Button>
                        </div>
                    </div>

                    <div />
                </div>
            </div>
        </div>
    </transition>
</template>