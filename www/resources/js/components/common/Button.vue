<script setup lang="ts">
import { computed } from 'vue'
import { ButtonProps } from '@/types'

const {
    type = 'button',
    href,
    class: classList,
    disabled
} = defineProps<ButtonProps>()

const emit = defineEmits<{
    click: [e: MouseEvent]
}>()

const tag = computed(() => (href ? 'a' : 'button'))
</script>

<template>
    <component :is="tag" :href :type :disabled :class="[
        'group h-10 px-3 flex justify-center items-center gap-1.5 cursor-pointer disabled:cursor-not-allowed',
        classList
    ]" @click.stop="emit('click', $event)">
        <slot />
    </component>
</template>
