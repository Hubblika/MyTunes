<script setup lang="ts">
import { ref, computed, onBeforeUnmount } from 'vue'
import { ButtonProps } from '@/types'

const {
    type = 'button',
    href,
    class: classList,
    disabled,
    tooltip
} = defineProps<ButtonProps>()

const emit = defineEmits<{ click: [e: MouseEvent] }>()

const tag = computed(() => (href ? 'a' : 'button'))

const buttonRef = ref<HTMLElement | null>(null)
const tooltipVisible = ref(false)
const tooltipStyle = ref<Record<string, string>>({})

function showTooltip() {
    if (!buttonRef.value) return

    const rect = buttonRef.value.getBoundingClientRect()
    const margin = 8
    const tooltipHeight = 32

    let top = rect.top - tooltipHeight - margin
    let position = 'top'

    if (top < 8) {
        top = rect.bottom + margin
        position = 'bottom'
    }

    tooltipStyle.value = {
        left: `${rect.left + rect.width / 2}px`,
        top: `${top}px`,
        transform: 'translateX(-50%)'
    }

    tooltipVisible.value = true
}

function hideTooltip() {
    tooltipVisible.value = false
}
</script>

<template>
    <component ref="buttonRef" :is="tag" :href :type :disabled :class="[
        'group h-10 px-3 flex justify-center items-center gap-1.5 cursor-pointer disabled:cursor-not-allowed',
        classList
    ]" @mouseenter="showTooltip" @mouseleave="hideTooltip" @click.stop="emit('click', $event)">
        <slot />
    </component>

    <Teleport to="body">
        <div v-if="tooltip && tooltipVisible" :style="tooltipStyle" class="fixed z-9999 pointer-events-none
               px-3 py-1 text-xs rounded-2xl
               border border-black/10 dark:border-white/10
               bg-white/20 dark:bg-white/5
               backdrop-blur-xl
               shadow-xl dark:shadow-2xl
               text-black dark:text-white
               transition-opacity duration-150
               opacity-100 whitespace-nowrap">
            {{ tooltip }}
        </div>
    </Teleport>
</template>
