<script setup lang="ts">
import { ref, onMounted, onUnmounted, nextTick } from 'vue'
import { SongCard, Icon } from '.'

const scrollContainer = ref<HTMLElement | null>(null)

const canScroll = ref(false)

function updateScroll() {
    if (!scrollContainer.value) return
    canScroll.value = scrollContainer.value.scrollWidth > scrollContainer.value.clientWidth
}

onMounted(() => {
    nextTick(() => updateScroll())
    window.addEventListener('resize', updateScroll)
})

onUnmounted(() => {
    window.removeEventListener('resize', updateScroll)
})

function scroll(direction: 'left' | 'right') {
    if (!scrollContainer.value) return

    const scrollAmount = 188
    scrollContainer.value.scrollBy({
        left: direction === 'left' ? -scrollAmount : scrollAmount,
        behavior: 'smooth'
    })
}
</script>

<template>
    <div class="relative w-full pl-3">
            <h1 class="text-lg font-medium">
                Section 1
            </h1>

            <div v-if="canScroll" class="absolute top-0 right-0 flex gap-2">
                <button type="button" @click="scroll('left')" class="inline-flex items-center justify-center rounded-full
             bg-cyan-500 text-white hover:bg-cyan-200
             transition-colors h-9 w-9">
                    <Icon name="arrow-badge-left-filled" />
                </button>

                <button type="button" @click="scroll('right')" class="inline-flex items-center justify-center rounded-full
             bg-cyan-500 text-white hover:bg-cyan-200
             transition-colors h-9 w-9">
                    <Icon name="arrow-badge-right-filled"/>
                </button>
            </div>
        </div>

        <section ref="scrollContainer" class="flex overflow-x-auto overflow-y-hidden scroll-smooth snap-x snap-mandatory no-scrollbar">
            <div v-for="i in 10" :key="i" class="shrink-0 w-48 snap-start">
                <SongCard title="Rise Up" image="/uploads/thumbnails/playlist/defaultThumbnail.png" />
            </div>
        </section>
</template>