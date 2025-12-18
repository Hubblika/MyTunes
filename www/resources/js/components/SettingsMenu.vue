<script setup lang="ts">
import { ToggleSwitch, Icon } from './common';
import { ref, watch, onMounted } from 'vue';

const isDark = ref(false);
const isEnglish = ref(true);

watch(isDark, (value) => {
    document.documentElement.classList.toggle('dark', value);
    localStorage.setItem('theme', value ? 'dark' : 'light');
})

onMounted(() => {
    isDark.value = localStorage.getItem('theme') === 'dark'
})
</script>

<template>
    <div class="flex flex-col gap-4">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">General Settings</h1>

        <div
            class="flex justify-between items-center w-full p-4 bg-gray-100 dark:bg-black rounded-xl shadow-inner transition-colors duration-300 hover:bg-gray-200 dark:hover:bg-gray-700">

            <p class="text-gray-800 dark:text-gray-200 font-medium">
                Theme - switch between light and dark mode
            </p>

            <div class="flex items-center gap-3">
                <p class="text-gray-800 dark:text-gray-200 font-semibold">
                    {{ isDark ? 'Dark' : 'Light' }}
                </p>

                <Icon :name="isDark ? 'moon' : 'sun'" class="w-5 h-5 text-yellow-500 dark:text-white"/>

                <ToggleSwitch v-model="isDark"/>
            </div>
        </div>

        <div
            class="flex justify-between items-center w-full p-4 bg-gray-100 dark:bg-black rounded-xl shadow-inner transition-colors duration-300 hover:bg-gray-200 dark:hover:bg-gray-700">

            <p class="text-gray-800 dark:text-gray-200 font-medium">
                Theme - switch app language (EN/HUN)
            </p>

            <div class="flex items-center gap-3">
                <p class="text-gray-800 dark:text-gray-200 font-semibold">
                    {{ isEnglish ? 'English' : 'Magyar' }}
                </p>

                <ToggleSwitch v-model="isEnglish"/>
            </div>
        </div>
    </div>
</template>