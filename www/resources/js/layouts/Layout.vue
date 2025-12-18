<script setup lang="ts">
import { onMounted, ref, useTemplateRef } from 'vue';

const layout = useTemplateRef('layout');

const dark = ref(true);

onMounted(() => {
    const defaultTheme: 'light' | 'dark' = 'light';
    let theme = localStorage.getItem('mytunes:theme');

    if (theme === null) {
        localStorage.setItem('mytunes:theme', defaultTheme);
        theme = defaultTheme;
    }

    dark.value = theme === 'dark';
});
</script>

<template>
    <div id="layout" :class="['w-full min-h-screen flex flex-col **:transition-colors **:duration-250', { dark }]" ref="layout">
        <slot></slot>
    </div>
</template>
