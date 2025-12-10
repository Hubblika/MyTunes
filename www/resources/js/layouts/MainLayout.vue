<script setup lang="ts">
import { onMounted, ref, useTemplateRef } from 'vue';
import { Header, MainContent, Sidebar, Toolbar } from '@/components';

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
        <Header></Header>
        <main class="flex-1 text-black dark:text-white bg-white dark:bg-black w-full flex flex-row gap-2 pl-2 pr-2">
            <slot>
                <Sidebar></Sidebar>
                <MainContent></MainContent>
            </slot>
        </main>
        <Toolbar></Toolbar>
    </div>
</template>
