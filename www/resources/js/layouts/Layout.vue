<script setup lang="ts">
import { onMounted, ref, watch } from 'vue'

const dark = ref(false)

onMounted(() => {
    const theme = (localStorage.getItem('theme') as 'light' | 'dark') ?? 'light'
    dark.value = theme === 'dark'
    document.documentElement.classList.toggle('dark', dark.value)
})

watch(dark, (value) => {
    document.documentElement.classList.toggle('dark', value)
    localStorage.setItem('theme', value ? 'dark' : 'light')
})
</script>

<template>
    <div id="layout" class="relative min-h-screen w-full overflow-hidden
           bg-neutral-100 text-neutral-900
           dark:bg-neutral-950 dark:text-white
           **:transition-colors **:duration-200">
        <div class="pointer-events-none fixed inset-0 z-0">
            <div class="absolute -top-40 -left-40 h-96 w-96 rounded-full bg-fuchsia-500/20 blur-3xl" />
            <div class="absolute top-1/3 -right-40 h-96 w-96 rounded-full bg-cyan-400/20 blur-3xl" />
        </div>
        <slot />
    </div>
</template>
