<script setup lang="ts">
import { onMounted, ref, useTemplateRef } from 'vue';
import { Loginform } from '@/components';

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
    <div id="layout" class="flex items-center justify-center w-full" :class="['**:transition-colors **:duration-250', { dark }]" ref="layout">
        <main
            class="text-black dark:text-white bg-white dark:bg-black 
                w-full">
            <Loginform></Loginform>
        </main>
    </div>
</template>
