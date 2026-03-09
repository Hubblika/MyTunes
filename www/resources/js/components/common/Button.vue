<script setup lang="ts">
import { ref, computed, onMounted, onBeforeUnmount, nextTick } from 'vue'
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
const tooltipRef = ref<HTMLElement | null>(null)

let hoverMedia: MediaQueryList | null = null

async function showTooltip() {
    if (!buttonRef.value) return

    const rect = buttonRef.value.getBoundingClientRect()
    const margin = 8
    const tooltipHeight = 32

    // 1. Initial Top Calculation
    let top = rect.top - tooltipHeight - margin
    if (top < 8) top = rect.bottom + margin

    // 2. Initial Horizontal Position (Centered)
    tooltipStyle.value = {
        left: `${rect.left + rect.width / 2}px`,
        top: `${top}px`,
        transform: 'translateX(-50%)',
        opacity: '0' // Hide it for a split second while we calculate
    }

    tooltipVisible.value = true

    // 3. Wait for DOM update to measure the actual tooltip width
    await nextTick()
    if (!tooltipRef.value) return

    const tooltipRect = tooltipRef.value.getBoundingClientRect()
    const screenWidth = window.innerWidth
    const padding = 12 // Minimum distance from screen edge

    let finalLeft = rect.left + rect.width / 2
    let finalTransform = 'translateX(-50%)'

    // 4. Boundary Checks
    // Check Left Edge
    if (tooltipRect.left < padding) {
        finalLeft = padding
        finalTransform = 'none'
    } 
    // Check Right Edge
    else if (tooltipRect.right > screenWidth - padding) {
        finalLeft = screenWidth - padding
        finalTransform = 'translateX(-100%)'
    }

    tooltipStyle.value = {
        left: `${finalLeft}px`,
        top: `${top}px`,
        transform: finalTransform,
        opacity: '1'
    }
}

function hideTooltip() {
    tooltipVisible.value = false
}

onMounted(() => {
    hoverMedia = window.matchMedia('(hover: hover)')
})

onBeforeUnmount(() => {
    hoverMedia = null
})
</script>

<template>
    <component ref="buttonRef" :is="tag" :href="href" :type="type" :disabled="disabled" :class="[
        'group h-10 px-3 flex justify-center items-center cursor-pointer disabled:cursor-not-allowed',
        classList
    ]" @mouseenter="hoverMedia?.matches && showTooltip()" @mouseleave="hoverMedia?.matches && hideTooltip()"
        @click.stop="emit('click', $event)">
        <slot />
    </component>

    <Teleport to="body">
        <div v-if="tooltip && tooltipVisible" ref="tooltipRef" :style="tooltipStyle" class="fixed z-9999 pointer-events-none
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
