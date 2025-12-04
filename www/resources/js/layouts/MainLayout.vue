<script setup lang="ts">
import { onMounted, ref, useTemplateRef } from 'vue';
import { Header } from '@/components';
import { Toolbar } from '@/components';
import { Sidebar } from '@/components';

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
    <div id="layout" :class="['**:transition-colors **:duration-250', { dark }]" ref="layout">
        <Header></Header>
        <main class="text-black dark:text-white bg-white dark:bg-black w-full min-h-screen flex flex-col gap-2">
            <slot><Sidebar></Sidebar></slot>
        </main>
        <Toolbar></Toolbar>
    </div>
</template>
