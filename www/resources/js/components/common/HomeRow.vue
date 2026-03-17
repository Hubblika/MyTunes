<script setup lang="ts">
import { ref, onMounted, onUnmounted, nextTick } from 'vue';
import { SongCard, Icon } from '.';
import { _Song } from '@/types';;

const props = defineProps<{
    title: string,
    songs: _Song[]
}>()

const scrollContainer = ref<HTMLElement | null>(null);
const canScroll = ref(false);

onMounted(() => {
    nextTick(() => updateScroll());
    window.addEventListener('resize', updateScroll);
});

onUnmounted(() => {
    window.removeEventListener('resize', updateScroll);
});

function scroll(direction: 'left' | 'right') {
    if (!scrollContainer.value) return;

    const scrollAmount = 188;
    scrollContainer.value.scrollBy({
        left: direction === 'left' ? -scrollAmount : scrollAmount,
        behavior: 'smooth'
    })
}

function updateScroll() {
    if (!scrollContainer.value) return;
    canScroll.value = scrollContainer.value.scrollWidth > scrollContainer.value.clientWidth;
}
</script>

<template>
    <div class="relative w-full pl-3">
        <h1 class="text-lg font-medium">
            {{ title }}
        </h1>

        <div v-if="canScroll" class="absolute top-0 right-0 flex">
            <button @click="scroll('left')" class="inline-flex items-center justify-center rounded-full
                bg-cyan-500 text-white hover:bg-cyan-200 transition-colors h-9 w-9">
                <Icon name="arrow-badge-left-filled" />
            </button>

            <button @click="scroll('right')" class="inline-flex items-center justify-center rounded-full
                bg-cyan-500 text-white hover:bg-cyan-200 transition-colors h-9 w-9">
                <Icon name="arrow-badge-right-filled" />
            </button>
        </div>
    </div>

    <section ref="scrollContainer"
        class="flex overflow-x-auto overflow-y-hidden scroll-smooth snap-x snap-mandatory no-scrollbar">
        <div v-for="song in songs" :key="song.uuid" class="shrink-0 w-38 snap-start">
            <SongCard :title="song.title"
                :image="song.cover_url ?? '/uploads/thumbnails/playlist/defaultThumbnail.png'" :song="song"/>
        </div>
    </section>
</template>
