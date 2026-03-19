<script setup lang="ts">
import { Icon } from '@/components/common'
import { router } from '@inertiajs/vue3'

function goHome() {
    router.get('/')
}

function goSearch() {
    router.get('/search')
}

function goLibrary() {
    router.get('/library')
}

function goSettings() {
    router.get('/settings')
}
</script>

<template>
    <div class="fixed bottom-4 left-1/2 -translate-x-1/2 z-40 w-[95%] max-w-md lg:hidden">
        <div
            class="grid grid-cols-4 text-center
                   p-2
                   rounded-4xl
                   border border-black/10 dark:border-white/10
                   bg-white/20 dark:bg-white/5
                   backdrop-blur-xl
                   shadow-xl dark:shadow-2xl">

            <button
                @click="goHome"
                class="flex flex-col items-center justify-center gap-1 py-1 w-full text-gray-500 dark:text-gray-400 active:scale-95 transition">
                <Icon name="home" class="size-5" />
                <span class="text-[10px] leading-none">Home</span>
            </button>

            <button
                @click="goSearch"
                class="flex flex-col items-center justify-center gap-1 py-1 w-full text-gray-500 dark:text-gray-400 active:scale-95 transition">
                <Icon name="search" class="size-5" />
                <span class="text-[10px] leading-none">Search</span>
            </button>

            <button
                @click="goLibrary"
                class="flex flex-col items-center justify-center gap-1 py-1 w-full text-gray-500 dark:text-gray-400 active:scale-95 transition">
                <Icon name="library" class="size-5" />
                <span class="text-[10px] leading-none">Library</span>
            </button>

            <button
                @click="goSettings"
                class="flex flex-col items-center justify-center gap-1 py-1 w-full text-gray-500 dark:text-gray-400 active:scale-95 transition">
                <Icon name="settings" class="size-5" />
                <span class="text-[10px] leading-none">Settings</span>
            </button>

        </div>
    </div>
</template>
