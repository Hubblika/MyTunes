<script setup lang="ts">
import { onMounted, ref, onBeforeUnmount } from 'vue';
import { Icon, Searchbar, Button } from './common';
import { router } from '@inertiajs/vue3';

const svg = ref('');
const dropdownOpen = ref(false);
const dark = ref(false);
const logoSrc = ref('');

function updateLogo() {
  const isDark = document.documentElement.classList.contains('dark');
  logoSrc.value = isDark ? '/uploads/logos/logo_dark.svg' : '/uploads/logos/logo_light.svg';
}

const logout = () => {
  router.post('/logout');
};

const selectMenu = (item: string) => {
    dropdownOpen.value = false;
    if (item === 'logout') logout();
    else if (item === 'settings') router.get('/settings');
    else console.log(`Selected ${item}`);
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
    console.log('Starting download');
    //TODO: download the desktop app
}

onMounted(() => {
    updateLogo();
    document.addEventListener('click', handleClickOutside);

    const observer = new MutationObserver(updateLogo);
    observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
});

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside)
})

</script>

<template>
    <header class="flex items-center justify-between
    bg-white dark:bg-black backdrop-blur-md
    w-full h-14 sticky top-0 left-0
    text-black dark:text-white pr-4 shrink-0 z-50">

        <div class="flex items-center gap-3 flex-none">
            <Button @click="loadHomeContent" class="size-20 p-0 flex justify-center items-center">
                <img :src="logoSrc" alt="Logo" class="size-25" />
            </Button>
        </div>

        <div class="absolute left-1/2 transform -translate-x-1/2 flex items-center gap-4">
            <div class="flex items-center gap-4">
                <Button @click="loadHomeContent"
                    class="size-10 rounded-full border border-black dark:border-white flex items-center justify-center cursor-pointer transition-all duration-150 hover:border-black/60 dark:hover:border-white/80 group">
                    <Icon name="home"
                        class="size-5 transition-colors group-hover:text-black/60 dark:group-hover:text-white/80" />
                </Button>
                <Searchbar class="w-96" :placeholder="$t('header.searchbar')" />
            </div>
        </div>

        <div class="flex items-center gap-4 flex-none">

            <Button @click="download"
                class="w-full rounded-full border border-black dark:border-white flex items-center justify-center cursor-pointer transition-all duration-150 hover:border-black/60 dark:hover:border-white/80 group">
                <Icon name="download"
                    class="size-5 transition-colors group-hover:text-black/60 dark:group-hover:text-white/80" />
                <span class="transition-colors group-hover:text-black/60 dark:group-hover:text-white/80">{{ $t('header.downloadButton') }}</span>
            </Button>

            <div class="relative" ref="dropdownRef">
                <button @click="dropdownOpen = !dropdownOpen" class="size-8 rounded-full bg-gray-300 dark:bg-gray-700 flex items-center justify-center
                       cursor-pointer hover:bg-gray-400 dark:hover:bg-gray-600 transition">
                    <Icon name="user" />
                </button>

                <ul v-if="dropdownOpen"
                    class="absolute right-0 mt-2 w-48 bg-white dark:bg-black border border-gray-200 dark:border-gray-500/6 rounded-md shadow-lg py-1 z-50">
                    <li @click="selectMenu('profile')"
                        class="flex justify-between px-4 py-2 hover:bg-gray-500/10 dark:hover:bg-white/10 cursor-pointer">
                        <span>{{ $t('header.profileButton') }}</span>
                        <Icon name="user"></Icon>
                    </li>
                    <li @click="selectMenu('settings')"
                        class="flex justify-between px-4 py-2 hover:bg-gray-500/10 dark:hover:bg-white/10 cursor-pointer">
                        <span>{{ $t('header.settingButton') }}</span>
                        <Icon name="settings"></Icon>
                    </li>
                    <li @click="selectMenu('logout')"
                        class="flex justify-between px-4 py-2 hover:bg-gray-500/10 dark:hover:bg-white/10 cursor-pointer">
                        <span>{{ $t('header.logoutButton') }}</span>
                        <Icon name="logout"></Icon>
                    </li>
                </ul>
            </div>

        </div>
    </header>
</template>
