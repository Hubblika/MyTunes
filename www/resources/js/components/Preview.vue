<script setup lang="ts">
import type { Playlist, Song } from '@/lib/types';
import { Icon } from './common';
import { computed } from 'vue';

const {
    self
} = defineProps<{
    self: Playlist | Song
}>();

const playlist = computed(() => Object.hasOwn(self, 'name') ? self as Playlist : null);
const song = computed(() => Object.hasOwn(self, 'title') ? self as Song : null);

const isLikedSongs = computed(() => playlist.value?.name === 'Liked songs');
</script>

<template>
    <article class="w-72 flex flex-col gap-0.5">
        <div :class="['w-full aspect-square border rounded-md', { 'bg-[#ff00ff20] dark:bg-pink-400': isLikedSongs }]">
            <div v-if="isLikedSongs" class="h-full flex justify-center items-center">
                <Icon name="heart" class="size-14"></Icon>
            </div>
            <img v-else src="/uploads/thumbnails/example.png" :alt="`Cover art for ${playlist?.name || song?.title}`" class="object-cover object-center size-full rounded-md" draggable="false" />
        </div>
        <span v-if="!!playlist" class="text-gray-600 dark:text-gray-300 line-clamp-1 w-full overflow-ellipsis"><span class="font-semibold">{{ playlist.name }}</span> &middot; n songs</span>
        <span v-else class="text-gray-600 dark:text-gray-300 line-clamp-1 w-full overflow-ellipsis"><span class="font-semibold">{{ song!.title }}</span> &middot; [Artist name]</span>
    </article>
</template>
