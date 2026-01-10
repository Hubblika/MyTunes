<script setup lang="ts">
import { onMounted, ref, watch } from 'vue'

const dark = ref(false)

onMounted(() => {
  const defaultTheme: 'light' | 'dark' = 'light'
  let theme = localStorage.getItem('theme') as 'light' | 'dark' | null

  if (!theme) {
    theme = defaultTheme
    localStorage.setItem('theme', theme)
  }

  dark.value = theme === 'dark'
})

watch(dark, (value) => {
  document.documentElement.classList.toggle('dark', value)
  localStorage.setItem('theme', value ? 'dark' : 'light')
})
</script>

<template>
  <div
    id="layout"
    class="w-full h-screen overflow-hidden flex flex-col **:transition-colors **:duration-250">
    <slot/>
  </div>
</template>
