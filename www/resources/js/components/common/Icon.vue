<script setup lang="ts">
import { onMounted, useTemplateRef, watch } from 'vue';
import { icons as tabler } from '@iconify-json/tabler';
import { ClassValue } from 'clsx';

const {
    name,
    class: classList = 'size-5'
} = defineProps<{
    name: string,
    class?: ClassValue
}>();

const svg = useTemplateRef('svg');

watch(() => name, setIcon);

onMounted(() => setIcon(name));

function setIcon(name: string) {
    if (svg.value) {
        svg.value.innerHTML = tabler.icons[name].body;
    }
}
</script>

<template>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" :class="classList" ref="svg"></svg>
</template>
