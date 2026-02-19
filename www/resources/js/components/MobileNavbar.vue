<script setup lang="ts">
import { Icon, Button } from '@/components/common'
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
            class="grid grid-cols-4 items-center text-center
                 p-3
                 rounded-4xl
                 border border-black/10 dark:border-white/10
                 bg-white/20 dark:bg-white/5
                 backdrop-blur-xl
                 shadow-xl dark:shadow-2xl">

            <Button @click="goHome" class="flex flex-col items-center justify-center gap-1 py-1 text-gray-500 dark:text-gray-400">
                <Icon name="home" class="size-5" />
                <span class="text-[10px]">Home</span>
            </Button>

            <Button @click="goSearch" class="flex flex-col items-center justify-center gap-1 py-1 text-gray-500 dark:text-gray-400">
                <Icon name="search" class="size-5" />
                <span class="text-[10px]">Search</span>
            </Button>

            <Button @click="goLibrary" class="flex flex-col items-center justify-center gap-1 py-1 text-gray-500 dark:text-gray-400">
                <Icon name="library" class="size-5" />
                <span class="text-[10px]">Library</span>
            </Button>

            <Button @click="goSettings" class="flex flex-col items-center justify-center gap-1 py-1 text-gray-500 dark:text-gray-400">
                <Icon name="settings" class="size-5" />
                <span class="text-[10px]">Settings</span>
            </Button>

        </div>
    </div>
</template>
