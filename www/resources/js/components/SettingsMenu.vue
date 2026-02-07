<script setup lang="ts">
import { ToggleSwitch, Icon } from './common';
import { ref, watch, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';

const { locale } = useI18n();

const isDark = ref(false);
const isEnglish = ref(true);

watch(isDark, (value) => {
    document.documentElement.classList.toggle('dark', value);
    localStorage.setItem('theme', value ? 'dark' : 'light');
})

watch(isEnglish, (value) => {
    const lang = value ? 'en' : 'hu';
    locale.value = lang;
    localStorage.setItem('lang', lang);
})

onMounted(() => {
    isDark.value = localStorage.getItem('theme') === 'dark';

    const savedLang = localStorage.getItem('lang') || 'en';
    isEnglish.value = savedLang === 'en';
    locale.value = savedLang;
})
</script>

<template>
    <div class="flex flex-col gap-4">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $t('settings.title') }}</h1>

        <div
            class="flex justify-between items-center w-full p-4 rounded-xl shadow-inner transition-colors duration-300 hover:bg-gray-500/10 dark:hover:bg-white/10">

            <p class="text-gray-800 dark:text-gray-200 font-medium">
                {{ $t('settings.themeTitle') }}
            </p>

            <div class="flex items-center gap-3">
                <p class="text-gray-800 dark:text-gray-200 font-semibold">
                    {{ isDark ? $t('settings.darkTheme') : $t('settings.lightTheme') }}
                </p>

                <Icon :name="isDark ? 'moon' : 'sun'" class="w-5 h-5 text-yellow-500 dark:text-white" />

                <ToggleSwitch v-model="isDark" />
            </div>
        </div>

        <div
            class="flex justify-between items-center w-full p-4 rounded-xl shadow-inner transition-colors duration-300 hover:bg-gray-500/10 dark:hover:bg-white/10">

            <p class="text-gray-800 dark:text-gray-200 font-medium">
                {{ $t('settings.languageTitle') }}
            </p>

            <div class="flex items-center gap-3">
                <p class="text-gray-800 dark:text-gray-200 font-semibold">
                    {{ $t('settings.languageLabel') }}
                </p>

                <ToggleSwitch v-model="isEnglish" />
            </div>
        </div>
    </div>
</template>