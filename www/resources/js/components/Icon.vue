<script setup lang="ts">
import { onMounted, useTemplateRef, watch } from 'vue';
import { ClassValue } from 'clsx';
import { icons as tabler } from '@iconify-json/tabler';

const {
    name,
    class: classList = 'size-5'
} = defineProps<{
    name: string,
    class?: ClassValue
}>();

const svg = useTemplateRef('svg');

function setIcon(name: string) {
    if (svg.value) {
        svg.value.innerHTML = tabler.icons[name].body;
    }
}

onMounted(() => setIcon(name));
watch(() => name, setIcon);
</script>

<template>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" :class="classList" ref="svg"></svg>
</template>
