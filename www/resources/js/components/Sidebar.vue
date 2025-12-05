<script setup lang="ts">
import Searchbar from './Searchbar.vue';
</script>

<template>
  <aside
    class="w-96 h-full p-4
           bg-gray-500/6 dark:bg-white/3
           dark:bg-black dark:text-white
           rounded-lg"
  >
    <div class="flex items-center gap-3 mb-6">
      <h1 class="text-lg font-semibold">Zenéim</h1>
    </div>
    <Searchbar></Searchbar>
  </aside>
</template>
