<script setup lang="ts">
import { computed } from 'vue';
import { ClassValue } from 'clsx';

const {
    href,
    type = 'button',
    class: classList
} = defineProps<{
    href?: string,
    type?: 'button' | 'submit' | 'menu' | 'reset',
    class?: ClassValue
}>();

defineEmits(['click']);

const tag = computed(() => href ? 'a' : 'button');
</script>

<template>
    <component :is="tag" :href :type :class="['h-10 px-3 flex justify-center items-center gap-1.5 cursor-pointer', classList]" @click="$emit('click')">
        <slot></slot>
    </component>
</template>
