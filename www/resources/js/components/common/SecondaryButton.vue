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
    <Button :href :type :class="['group text-black dark:text-white hover:bg-gray-100/50 border border-gray-500/50 dark:border-white rounded-lg shadow-sm transition-all cursor-pointer disabled:cursor-not-allowed', classList]" :disabled @click="$emit('click')">
        <slot></slot>
    </Button>
</template>
