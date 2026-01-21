<script setup lang="ts">
import { Button } from '.';
import { ButtonProps } from '@/types';

const {
    type = 'button',
    href,
    class: classList,
    disabled
} = defineProps<ButtonProps>();

defineEmits(['click']);
</script>

<template>
    <Button :href :type :class="['group font-semibold text-black dark:text-white bg-white hover:bg-pink-500 dark:bg-[#181818] w-full border border-pink-500/50 rounded-lg shadow-sm dark:shadow-lg dark:shadow-pink-500/10 transition-all cursor-pointer disabled:cursor-not-allowed', classList]" :disabled @click="$emit('click')">
        <slot></slot>
    </Button>
</template>
