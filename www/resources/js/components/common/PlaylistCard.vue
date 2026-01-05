<script setup lang="ts">
import { ref, onMounted } from 'vue'
import {Icon, Button } from '.';

const dropdownOpen = ref(false);

const props = defineProps<{
    id: number
    title: string
    subtitle?: string
    coverUrl?: string
}>()

const emit = defineEmits<{
  (e: 'deletePlaylist', id: number): void
}>()

const selectMenu = (item: string) => {
    dropdownOpen.value = false;
    if (item === 'delete') emit('deletePlaylist', props.id);
    else console.log(`Selected ${item}`);
}

const dropdownRef = ref<HTMLElement | null>(null);
const handleClickOutside = (event: MouseEvent) => {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target as Node)) {
        dropdownOpen.value = false;
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});
</script>

<template>
    <div class="flex items-center gap-3 p-2 rounded-md cursor-pointer hover:bg-gray-500/10 dark:hover:bg-white/10 transition">
        <div class="size-12 rounded bg-gray-400/30 dark:bg-white/20 overflow-hidden shrink-0">
            <img :src="coverUrl" class="w-full h-full object-cover"/>
        </div>
        <div class="min-w-0 grow">
           <p class="text-sm font-medium truncate">{{ title }}</p>
                <p v-if="subtitle" class="text-xs opacity-60 truncate">
                    {{ subtitle }}
                </p>
        </div>
        <div v-if="title !== 'Liked Songs' && title !== 'Kedvelt Zenéim'" class="relative" ref="dropdownRef">
            <Button @click="dropdownOpen = !dropdownOpen" class="group transition-all duration-150"><Icon name="dots-vertical" class="size-5 transition-transform duration-150 group-hover:scale-110"></Icon></Button>
                <ul v-if="dropdownOpen" class="absolute mt-2 w-48 bg-white dark:bg-black border border-gray-200 dark:border-gray-500/6 rounded-md shadow-lg py-1 z-50 right-0">
                    <li @click="selectMenu('delete')" class="flex justify-between px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer">
                        <span>Delete playlist</span>
                        <Icon name="trash" class="size-5 text-red-500"></Icon>
                    </li>
                </ul>
        </div>
    </div>
</template>
