<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { Icon, SecondaryButton, Searchbar, Button } from './common';
import { router } from '@inertiajs/vue3';
import { ApiResult } from '@/types';

const svg = ref('');
const dropdownOpen = ref(false);

async function resetSvg() {
    // svg.value = document.querySelector('#layout')?.classList.contains('light')
    //     ? await import('@/lib/logo_dark.svg?raw').then(r => r.default)
    //     : await import('@/lib/logo_light.svg?raw').then(r => r.default)
}

const logout = async () => {
    const data: ApiResult<any> = await fetch('/api/account/logout', {
        method: 'post',
        credentials: 'include'
    }).then(r => r.json());

    if (data.error) {
        alert(`${data.error.name}: ${data.error.message}`);
        return;
    }

    router.get('/login?loggedout=1');
}

const selectMenu = (item: string) => {
    dropdownOpen.value = false;
    if (item === 'logout') logout();
    else console.log(`Selected ${item}`);
}

const dropdownRef = ref<HTMLElement | null>(null);
const handleClickOutside = (event: MouseEvent) => {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target as Node)) {
        dropdownOpen.value = false;
    }
}

function loadHomeContent() {
    console.log('Home content');
    //TODO: load home content into the maincontent component DO NOT redirect to the ./ directory
}

function download() {
    console.log('Starting download');
    //TODO: download the desktop app
}

onMounted(() => {
    resetSvg();
    document.addEventListener('click', handleClickOutside);
});

</script>

<template>
<header class="flex items-center justify-between
    bg-white dark:bg-black backdrop-blur-md
    w-full h-14 sticky top-0 left-0
    text-black dark:text-white">

    <div class="flex items-center gap-3 flex-none">
        <Button @click="loadHomeContent" class="size-20 p-0 flex justify-center items-center"></Button>
    </div>

    <div class="absolute left-1/2 transform -translate-x-1/2 flex items-center gap-4">
        <div class="flex items-center gap-4">
            <Button @click="loadHomeContent" class="size-10 rounded-full border border-black dark:border-white flex items-center justify-center cursor-pointer transition-all duration-150 hover:border-black/60 dark:hover:border-white/80 group">
                <Icon name="home" class="size-5 transition-colors group-hover:text-black/60 dark:group-hover:text-white/80"/>
            </Button>
            <Searchbar class="w-96"/>
        </div>
    </div>

    <div class="flex items-center gap-4 flex-none">

        <Button @click="download" class="w-full rounded-full border border-black dark:border-white flex items-center justify-center cursor-pointer transition-all duration-150 hover:border-black/60 dark:hover:border-white/80 group">
            <Icon name="download" class="size-5 transition-colors group-hover:text-black/60 dark:group-hover:text-white/80"/>
            <span class="transition-colors group-hover:text-black/60 dark:group-hover:text-white/80">Download the desktop app</span>
        </Button>

        <div class="relative" ref="dropdownRef">
            <button
                @click="dropdownOpen = !dropdownOpen"
                class="size-8 rounded-full bg-gray-300 dark:bg-gray-700 flex items-center justify-center
                       cursor-pointer hover:bg-gray-400 dark:hover:bg-gray-600 transition">
                <Icon name="user"/>
            </button>

            <ul v-if="dropdownOpen" class="absolute right-0 mt-2 w-48 bg-white dark:bg-black border border-gray-200 dark:border-gray-500/6 rounded-md shadow-lg py-1 z-50">
                <li @click="selectMenu('profile')" class="flex justify-between px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer">
                    <span>Profile</span>
                    <Icon name="user"></Icon>
                </li>
                <li @click="selectMenu('settings')" class="flex justify-between px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer">
                    <span>Settings</span>
                    <Icon name="settings"></Icon>
                </li>
                <li @click="selectMenu('logout')" class="flex justify-between px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer">
                    <span>Logout</span>
                    <Icon name="logout"></Icon>
                </li>
            </ul>
        </div>

    </div>
</header>
</template>
