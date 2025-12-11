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
        font-semibold text-black dark:text-white bg-white hover:bg-pink-500 dark:bg-[#181818]
        w-full
        border border-pink-500/50 rounded-lg shadow-sm dark:shadow-lg dark:shadow-pink-500/10
        transition-all
    " @click="$emit('click')">
        <slot></slot>
    </Button>
</template>
