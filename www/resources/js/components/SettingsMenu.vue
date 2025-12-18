<script setup lang="ts">
import { ToggleSwitch } from './common';
</script>

<template>
    <div class="flex flex-col color-black dark:color-white gap-4">
        <h1 class="font-bold">General</h1>
        <div class="flex justify-between">
            <p class="color-black dark:color-white">Theme - change between light and dark themes</p>
            <ToggleSwitch></ToggleSwitch>
        </div>
        <div class="flex justify-between">
            <p class="color-black dark:color-white">Language - change the language of the application</p>
            <ToggleSwitch></ToggleSwitch>
        </div>
    </div>
</template>