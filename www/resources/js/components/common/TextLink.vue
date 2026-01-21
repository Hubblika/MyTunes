<script setup lang="ts">
import { ClassValue } from 'clsx';

const {
    href,
    class: classList
} = defineProps<{
    href?: string,
    class?: ClassValue
}>();

defineEmits(['click']);
</script>

<template>
    <a :href :class="['text-indigo-600 dark:text-indigo-400 hover:underline cursor-pointer', classList]" @click="$emit('click')">
        <span>
            <slot></slot>
        </span>
    </a>
</template>
