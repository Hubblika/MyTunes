<script setup lang="ts">
import { computed } from 'vue';
import { ClassValue } from 'clsx';

const {
    href,
    class: classList
} = defineProps<{
    href?: string,
    class?: ClassValue
}>();

defineEmits(['click']);

const tag = computed(() => href ? 'a' : 'button');
</script>

<template>
    <component :is="tag" :class="['h-9 px-3 bg-transparent hover:bg rounded flex justify-center items-center gap-1.5 cursor-pointer', classList]" @click="$emit('click')">
        <slot></slot>
    </component>
</template>
