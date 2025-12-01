<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { Button, Icon, Searchbar } from '.';

const svg = ref('');

async function resetSvg() {
    svg.value = document.querySelector('#layout')?.classList.contains('dark')
        ? await import('@/lib/logo_dark.svg?raw').then(r => r.default)
        : await import('@/lib/logo_light.svg?raw').then(r => r.default)
}

async function toggleTheme() {
    const dark = localStorage.getItem('mytunes:theme')! === 'dark';

    localStorage.setItem('mytunes:theme', dark ? 'light' : 'dark');
    document.querySelector('#layout')?.classList.toggle('dark');
    await resetSvg();
}

onMounted(resetSvg)
</script>

<template>
    <header class="bg-gray-500/5 dark:bg-white/3 backdrop-blur-md w-full h-14 grid grid-rows-1 grid-cols-3 fixed top-0 left-0 *:h-full *:px-4 *:flex *:items-center *:gap-4">
        <div>
            <a href="/" class="size-14 flex justify-center items-center -translate-x-4" v-html="svg"></a>
        </div>
        <div class="justify-center">
            <Searchbar></Searchbar>
        </div>
        <div class="justify-end">
            <button class="size-7 flex justify-center items-center cursor-pointer" @click="toggleTheme">
                <Icon name="moon" class="text-black dark:text-white"></Icon>
            </button>
            <a href="/settings" class="size-7 flex justify-center items-center">
                <Icon name="settings" class="text-black dark:text-white"></Icon>
            </a>
            <Button>
                <span class="text-black dark:text-white">Log in</span>
            </Button>
        </div>
    </header>
</template>
