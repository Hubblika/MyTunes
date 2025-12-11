<script setup lang="ts">
import Searchbar from './Searchbar.vue';
</script>

<template>
    <aside class="bg-gray-500/6 dark:bg-black dark:text-white w-96 min-h-full px-4 rounded-lg">
        <div class="flex items-center h-16">
            <h1 class="text-lg font-bold">My music</h1>
        </div>
        <Searchbar></Searchbar>
    </aside>
</template>
