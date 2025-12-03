<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { Icon, Searchbar } from '.';

const svg = ref('');

async function resetSvg() {
    svg.value = document.querySelector('#layout')?.classList.contains('light')
        ? await import('@/lib/logo_dark.svg?raw').then(r => r.default)
        : await import('@/lib/logo_light.svg?raw').then(r => r.default)
}

onMounted(resetSvg)
</script>

<template>
    <header class="bg-gray-500/5 dark:bg-white/3 backdrop-blur-md w-full h-14 grid grid-rows-1 grid-cols-3 fixed top-0 left-0 *:h-full *:px-4 *:flex *:items-center *:gap-4">
        <div>
            <a href="/" class="size-14 flex justify-center items-center -translate-x-4" v-html="svg"></a>
        </div>
        <div class="justify-center flex items-center gap-4">
            <a href="/" class="w-10 h-10 rounded-full border border-gray-800 text-black flex items-center justify-center cursor-pointer hover:bg-gray-100 transition">
                <Icon name="home"></Icon>
            </a>

            <Searchbar></Searchbar>
        </div>
        <div class="justify-end flex items-center gap-4">
            <div class="flex items-center justify-center gap-2 px-3 py-1 border border-black text-black rounded-md cursor-pointer hover:bg-gray-100 transition">
                <Icon name="download" class="size-4"></Icon>
                <span>Download the desktop app</span>
            </div>

            <div class="w-8 h-8 rounded-full bg-gray-300 dark:bg-gray-700 flex items-center justify-center cursor-pointer hover:bg-gray-400 dark:hover:bg-gray-600 transition">
                <Icon name="user"></Icon>
            </div>
        </div>
    </header>
</template>
