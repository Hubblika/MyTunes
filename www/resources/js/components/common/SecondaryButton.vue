<script setup lang="ts">
import { Button } from '.';

const {
    href,
    type = 'button'
} = defineProps<{
    href?: string,
    type?: 'button' | 'submit' | 'menu' | 'reset'
}>();

defineEmits(['click']);
</script>

<template>
    <Button :href :type class="
        text-black dark:text-white hover:bg-gray-100/50
        border border-gray-500/50 dark:border-white rounded-lg shadow-sm
        transition-all
    " @click="$emit('click')">
        <slot></slot>
    </Button>
</template>
