<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { Icon, Searchbar } from '.';

const svg = ref('');

async function resetSvg() {
    svg.value = document.querySelector('#layout')?.classList.contains('light')
        ? await import('@/lib/logo_dark.svg?raw').then(r => r.default)
        : await import('@/lib/logo_light.svg?raw').then(r => r.default)
}

onMounted(resetSvg)
</script>

<template>
    <header
  class="flex items-center justify-between
         bg-white dark:bg-black backdrop-blur-md
         w-full h-14 sticky top-0 left-0
         text-black dark:text-white px-4">
  <div class="flex items-center gap-3 flex-none">
    <a href="/" class="size-12 flex justify-center items-center" v-html="svg"></a>
  </div>

  <div class="absolute left-1/2 transform -translate-x-1/2 flex items-center gap-4">
    <div class="flex items-center gap-4">
      <a href="/"
        class="w-10 h-10 rounded-full border border-black dark:border-white
               flex items-center justify-center cursor-pointer hover:bg-gray-100 transition">
        <Icon name="home" />
      </a>

      <Searchbar class="w-96"/>
    </div>
  </div>

  <div class="flex items-center gap-4 flex-none">
    <div
      class="flex items-center justify-center gap-2 px-3 py-1 border border-black
             dark:border-white text-black dark:text-white rounded-md cursor-pointer
             hover:bg-gray-100 transition">
      <Icon name="download" class="size-4" />
      <span>Download the desktop app</span>
    </div>

    <div
      class="w-8 h-8 rounded-full bg-gray-300 dark:bg-gray-700 flex items-center justify-center
             cursor-pointer hover:bg-gray-400 dark:hover:bg-gray-600 transition"
    >
      <Icon name="user"/>
    </div>
  </div>
</header>

</template>
