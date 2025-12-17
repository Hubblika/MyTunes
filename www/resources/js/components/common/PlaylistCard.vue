<script setup lang="ts">
defineProps<{
    title: string
    subtitle?: string
    coverUrl?: string
}>()
</script>

<template>
    <div class="flex items-center gap-3 p-2 rounded-md cursor-pointer hover:bg-gray-500/10 dark:hover:bg-white/10 transition">
        <div class="size-12 rounded bg-gray-400/30 dark:bg-white/20 overflow-hidden shrink-0">
            <img :src="coverUrl" class="w-full h-full object-cover"/>
        </div>
        <div class="min-w-0">
            <span class="text-sm font-medium truncate">{{ title }}</span>
            <span v-if="subtitle" class="text-xs opacity-60 truncate">
                {{ subtitle }}
            </span>
        </div>
    </div>
</template>
