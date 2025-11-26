<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { Icon, Searchbar } from '.';

const svg = ref('');

onMounted(async () => {
    svg.value = await import('@/lib/logo_light.svg?raw').then(r => r.default);
})
</script>

<template>
    <header class="bg-gray-500/5 backdrop-blur-md w-full h-14 grid grid-rows-1 grid-cols-3 fixed top-0 left-0 *:h-full *:px-4 *:flex *:items-center">
        <div>
            <a href="/" class="size-14 flex justify-center items-center -translate-x-4" v-html="svg"></a>
        </div>
        <div class="justify-center">
            <Searchbar></Searchbar>
        </div>
        <div class="justify-end">
            <a href="/settings" class="size-7 flex justify-center items-center">
                <Icon name="settings"></Icon>
            </a>
        </div>
    </header>
</template>
