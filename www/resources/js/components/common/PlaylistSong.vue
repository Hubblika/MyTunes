<script setup lang="ts">
import { Icon } from '.'
import { _Song } from '@/types';

defineProps<{
    index: number;
    song: _Song;
}>();
</script>

<template>
    <div class="group grid grid-cols-[32px_48px_1fr_1fr_120px_100px]
       items-center gap-4 px-4 py-2 rounded-md
       text-sm text-neutral-400 hover:bg-gray-500/10 dark:hover:bg-white/10 cursor-pointer">
        <div class="text-right group-hover:hidden">
            {{ index }}
        </div>

        <div class="hidden group-hover:flex justify-end">
            <Icon name="player-play-filled" class="size-6 text-black dark:text-white" />
        </div>

        <img :src="song.cover_url" alt="cover" class="w-10 h-10 rounded object-cover" />

        <div class="min-w-0">
            <div class="text-black dark:text-white font-medium truncate">
                {{ song.title }}
            </div>
            <div class="truncate">
                {{ song.artist }}
            </div>
        </div>

        <div class="min-w-0 truncate">
            xp formula
        </div>

        <div class="justify-self-start text-right tabular-nums">
            now
        </div>

        <!-- Duration -->
        <div class="justify-self-end text-right tabular-nums">
            {{ song.duration }}
        </div>
    </div>
</template>
