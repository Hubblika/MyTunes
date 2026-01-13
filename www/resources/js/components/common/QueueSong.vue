<script setup lang="ts">
import { Icon } from '.'

defineProps<{
    index: number
    cover: string
    title: string
    artist: string
    duration: string
}>()
</script>

<template>
    <div class="group grid grid-cols-[32px_48px_1fr_100px]
           items-center gap-4 px-4 py-2 rounded-md
           text-sm text-neutral-400 hover:bg-gray-500/10 dark:hover:bg-white/10 cursor-pointer">
        <div class="text-right group-hover:hidden">
            {{ index }}
        </div>

        <div class="hidden group-hover:flex justify-end">
            <Icon name="player-play-filled" class="size-6 text-black dark:text-white" />
        </div>

        <img :src="cover" alt="cover" class="w-10 h-10 rounded object-cover" />

        <div class="min-w-0">
            <div class="text-black dark:text-white font-medium truncate">
                {{ title }}
            </div>
            <div class="truncate">
                {{ artist }}
            </div>
        </div>

        <div class="justify-self-end text-right tabular-nums">
            {{ duration }}
        </div>
    </div>
</template>
