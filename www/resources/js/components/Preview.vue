<script setup lang="ts">
import { computed } from 'vue';
import type { Playlist, Song } from '@/types';

const {
    self
} = defineProps<{
    self: Playlist | Song
}>();

const playlist = computed(() => Object.hasOwn(self, 'name') ? self as Playlist : null);
const song = computed(() => Object.hasOwn(self, 'title') ? self as Song : null);
</script>

<template>
    <article class="w-64 flex flex-col gap-0.5">
        <div class="w-full aspect-square border rounded-md">
            <img :src="`/uploads/${!!playlist ? 'playlist' : 'song'}/cover.png`" :alt="`Cover art for ${playlist?.name || song?.title}`" class="object-cover object-center size-full" />
        </div>
        <span class="font-semibold text-gray-600 line-clamp-1 w-full overflow-ellipsis" v-if="!!playlist">{{ playlist.name }} &middot; n songs</span>
        <span class="font-semibold text-gray-600 line-clamp-1 w-full overflow-ellipsis" v-else>{{ song!.title }} &middot; [Artist name]</span>
    </article>
</template>
