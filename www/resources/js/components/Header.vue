<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { Button, Icon, Searchbar } from '.';

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
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M3 12l9-9 9 9h-3v9h-12v-9h-3z"/>
            </svg>
            </a>

            <Searchbar></Searchbar>
        </div>
        <div class="justify-end flex items-center gap-4">
            <div class="flex items-center justify-center px-3 py-1 border border-black text-black rounded-md cursor-pointer hover:bg-gray-100 transition">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M3 14a1 1 0 001 1h12a1 1 0 001-1v-2h-2v1H5v-1H3v2zM9 7h2v6h3l-4 4-4-4h3V7z"/>
                </svg>
                <span>Download the desktop app</span>
            </div>

            <div class="w-8 h-8 rounded-full bg-gray-300 dark:bg-gray-700 flex items-center justify-center cursor-pointer hover:bg-gray-400 dark:hover:bg-gray-600 transition">
                <svg class="w-4 h-4 text-gray-700 dark:text-gray-200" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                </svg>
            </div>
        </div>
    </header>
</template>
