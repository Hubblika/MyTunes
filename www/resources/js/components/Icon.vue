<script setup lang="ts">
import { onMounted, useTemplateRef, watch } from 'vue';
import { ClassValue } from 'clsx';

import icons from '@/lib/icons';

const {
    name,
    class: classList = 'size-6'
} = defineProps<{
    name: keyof typeof icons,
    class?: ClassValue
}>();

function setIcon(name: keyof typeof icons) {
    if (svg.value) {
        svg.value.innerHTML = icons[name];
    }
}

const svg = useTemplateRef('svg');

onMounted(() => setIcon(name));
watch(() => name, setIcon);
</script>

<template>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" :class="classList" ref="svg"></svg>
</template>
