<script setup lang="ts">
import { ToggleSwitch, Icon, Button } from './common';
import { ref, watch, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import { router } from '@inertiajs/vue3';

const { locale } = useI18n();

const isDark = ref(false);
const isEnglish = ref(true);

watch(isDark, (value) => {
    document.documentElement.classList.toggle('dark', value);
    localStorage.setItem('theme', value ? 'dark' : 'light');
});

watch(isEnglish, (value) => {
    const lang = value ? 'en' : 'hu';
    locale.value = lang;
    localStorage.setItem('lang', lang);
});

onMounted(() => {
    isDark.value = localStorage.getItem('theme') === 'dark';
    const savedLang = localStorage.getItem('lang') || 'en';
    isEnglish.value = savedLang === 'en';
    locale.value = savedLang;
});

const logout = () => {
    router.post('/logout');
};
</script>

<template>
    <div class="w-full h-full px-4 py-6 flex flex-col gap-6 sm:px-6 lg:px-8 lg:py-8">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
            {{ $t('settings.title') }}
        </h1>

        <div
            class="flex justify-between items-center w-full rounded-xl shadow-inner transition-colors duration-300 hover:bg-gray-500/10 dark:hover:bg-white/10 p-4">
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <Icon name="moon" class="w-6 h-6 text-yellow-500 dark:text-white shrink-0" />
                <div class="flex-1 min-w-0 wrap-break-word">
                    <p class="text-gray-800 dark:text-gray-200 font-semibold">
                        {{ $t('settings.themeTitle') }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $t('settings.themeDescription') }}
                    </p>
                </div>
            </div>

            <ToggleSwitch v-model="isDark" />
        </div>

        <div
            class="flex justify-between items-center w-full rounded-xl shadow-inner transition-colors duration-300 hover:bg-gray-500/10 dark:hover:bg-white/10 p-4">
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <Icon name="language" class="w-6 h-6 text-blue-500 dark:text-white shrink-0" />
                <div class="flex-1 min-w-0 wrap-break-word">
                    <p class="text-gray-800 dark:text-gray-200 font-semibold">
                        {{ $t('settings.languageTitle') }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $t('settings.languageDescription') }}
                    </p>
                </div>
            </div>

            <ToggleSwitch v-model="isEnglish" />
        </div>


        <Button @click="logout" class="w-full justify-center bg-red-500 hover:bg-red-600 text-white font-semibold rounded-xl px-4 py-2 transition-colors flex items-center gap-2
             lg:w-auto lg:px-3 lg:py-1.5 lg:text-sm">
            <Icon name="logout" class="w-5 h-5" />
            {{ $t('settings.logout') }}
        </Button>
    </div>
</template>
