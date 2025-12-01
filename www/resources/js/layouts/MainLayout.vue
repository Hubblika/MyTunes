<script setup lang="ts">
import { onMounted, ref, useTemplateRef } from 'vue';
import { Header } from '@/components';

const layout = useTemplateRef('layout');

const dark = ref(true);

onMounted(() => {
    let theme = localStorage.getItem('mytunes:theme');

    if (theme === null) {
        localStorage.setItem('mytunes:theme', 'dark');
        theme = 'dark';
    }

    dark.value = theme === 'dark';
});
</script>

<template>
    <div id="layout" :class="['**:transition-colors **:duration-250', { dark }]" ref="layout">
        <Header></Header>
        <main class="text-black dark:text-white bg-white dark:bg-black w-full min-h-screen px-8 pt-20 flex flex-col gap-2">
            <slot></slot>
        </main>
        <!-- <Footer></Footer> -->
    </div>
</template>
