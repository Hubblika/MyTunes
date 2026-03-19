<script setup lang="ts">
import { onMounted, ref, onBeforeUnmount, watch } from 'vue';
import { Icon, Searchbar, Button } from './common';
import { router } from '@inertiajs/vue3';
import _ from 'lodash';
import axios from 'axios';

const dropdownOpen = ref(false);
const logoSrc = ref('');
const email = ref("");

function updateLogo() {
    const isDark = document.documentElement.classList.contains('dark');
    logoSrc.value = isDark ? '/uploads/logos/logo_dark.svg' : '/uploads/logos/logo_light.svg';
}

const searchQuery = ref('');
watch(searchQuery, _.debounce((q: string) => {
    if (q.trim() !== '') {
        router.get('/search', { query: q }, { preserveState: true, replace: true });
    } else {
        router.get('/');
    }
}, 250));

const logout = () => {
    router.post('/logout');
};

const selectMenu = (item: string) => {
    dropdownOpen.value = false;
    if (item === 'logout') logout();
    else if (item === 'settings') router.get('/settings');
}

const dropdownRef = ref<HTMLElement | null>(null);
const handleClickOutside = (event: MouseEvent) => {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target as Node)) {
        dropdownOpen.value = false;
    }
}

function loadHomeContent() {
    router.get('/');
}

function download() {    
    const fileUrl = '/downloads/MyTunes_Desktop.exe';
    
    const link = document.createElement('a');
    link.href = fileUrl;
    
    link.setAttribute('download', 'MyTunes_Desktop.exe'); 
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

async function getUser() {
    const res = await axios.get('/me');
    email.value = res.data.data.email;
}

onMounted(async () => {
    updateLogo();
    document.addEventListener('click', handleClickOutside);
    await getUser()

    const observer = new MutationObserver(updateLogo);
    observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside)
})

</script>

<template>
    <header class="flex items-center justify-between
         h-14 sticky top-0 left-0 z-50 shrink-0 px-4
         rounded-2xl mx-4 mt-4
         border border-black/10 dark:border-white/10
           bg-white/20 dark:bg-white/5
         backdrop-blur-md
         shadow-md dark:shadow-xl
         transition-all duration-300">

        <div class="flex items-center gap-3 flex-none">
            <Button @click="loadHomeContent" class="size-20 p-0 flex justify-center items-center">
                <img :src="logoSrc" alt="Logo" class="size-25" />
            </Button>
        </div>

        <div class="absolute left-1/2 transform -translate-x-1/2 flex items-center gap-4">
            <div class="flex items-center gap-4">
                <Button @click="loadHomeContent" :tooltip="$t('tooltip.home')"
                    class="size-10 rounded-full border border-black dark:border-white flex items-center justify-center cursor-pointer transition-all duration-150 hover:border-black/60 dark:hover:border-white/80 group">
                    <Icon name="home"
                        class="size-5 transition-colors group-hover:text-black/60 dark:group-hover:text-white/80" />
                </Button>
                <Searchbar v-model="searchQuery" class="w-96" :placeholder="$t('header.searchbar')" />
            </div>
        </div>

        <div class="flex items-center gap-4 flex-none">

            <Button @click="download"
                class="w-full rounded-full border border-black dark:border-white flex items-center justify-center cursor-pointer transition-all duration-150 hover:border-black/60 dark:hover:border-white/80 group">
                <Icon name="download"
                    class="size-5 transition-colors group-hover:text-black/60 dark:group-hover:text-white/80" />
                <span class="transition-colors group-hover:text-black/60 dark:group-hover:text-white/80">{{
                    $t('header.downloadButton') }}</span>
            </Button>

            <div class="relative" ref="dropdownRef">
                <Button @click="dropdownOpen = !dropdownOpen" :tooltip="email" class="rounded-full bg-gray-300 dark:bg-gray-700 flex items-center justify-center
                       cursor-pointer hover:bg-gray-400 dark:hover:bg-gray-600 transition">
                    <Icon name="user" />
                </Button>

                <ul v-if="dropdownOpen" class="absolute right-0 mt-2 w-48
                text-black dark:text-white
                bg-white/20 dark:bg-black/20
                backdrop-blur-md
                border border-black/10 dark:border-white/10
                rounded-2xl shadow-lg py-2 z-50">
                    <li @click="selectMenu('settings')" class="flex justify-between items-center px-4 py-2 rounded-lg cursor-pointer
               hover:bg-white/30 dark:hover:bg-black/30 transition-colors duration-150">
                        <span>{{ $t('header.settingButton') }}</span>
                        <Icon name="settings" class="size-5" />
                    </li>

                    <li @click="selectMenu('logout')" class="flex justify-between items-center px-4 py-2 rounded-lg cursor-pointer
               hover:bg-white/30 dark:hover:bg-black/30 transition-colors duration-150">
                        <span>{{ $t('header.logoutButton') }}</span>
                        <Icon name="logout" class="size-5 text-red-500" />
                    </li>
                </ul>

            </div>

        </div>
    </header>
</template>
