<script setup lang="ts">
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { Icon } from './common';

const query = ref('');

function search() {
    router.push({ url: `/search/${encodeURIComponent(query.value)}` });
}
</script>

<template>
    <label class="w-3/4 h-11 border border-black dark:border-white rounded-full flex">
        <div class="h-full aspect-square flex justify-center items-center">
            <Icon name="search" class="text-black dark:text-white size-5 translate-x-0.5"></Icon>
        </div>
        <input id="search" type="text" class="font-semibold text-black dark:text-white placeholder-gray-600 dark:placeholder-gray-300 h-full outline-none grow placeholder:font-semibold" placeholder="Search" @submit="search" v-model.trim="query" />
        <button :class="['h-full aspect-square flex justify-center items-center cursor-pointer', { 'collapse': query.length < 1 }]" @click="search">
            <Icon name="send" class="size-5 -translate-x-0.5"></Icon>
        </button>
    </label>
</template>
