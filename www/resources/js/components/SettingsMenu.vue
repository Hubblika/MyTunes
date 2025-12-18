<script setup lang="ts">
import { ToggleSwitch } from './common';
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
    <div class="flex flex-col color-black dark:color-white gap-4">
        <h1 class="font-bold">General</h1>
        <div class="flex justify-between">
            <p class="color-black dark:color-white">Theme - change between light and dark themes</p>
            <ToggleSwitch v-model="isDark"></ToggleSwitch>
        </div>
        <div class="flex justify-between">
            <p class="color-black dark:color-white">Language - change the language of the application</p>
            <ToggleSwitch v-model="isEnglish"></ToggleSwitch>
        </div>
    </div>
</template>