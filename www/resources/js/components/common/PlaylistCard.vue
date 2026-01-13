<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Icon, Button } from '.';
import { router } from '@inertiajs/vue3';
import { Playlist } from '@/lib/types';

const dropdownOpen = ref(false);

const { playlist } = defineProps<{
    playlist: Playlist
}>()

const emit = defineEmits<{
    (e: 'deletePlaylist', id: string): void
}>();

const selectMenu = (item: string) => {
    dropdownOpen.value = false;
    if (item === 'delete') emit('deletePlaylist', playlist.id);
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
    <div class="flex items-center gap-3 p-2 rounded-md cursor-pointer hover:bg-gray-500/10 dark:hover:bg-white/10 transition" @click="() => router.visit(`/playlist/${playlist.id}`)">
        <div class="size-12 rounded bg-gray-400/30 dark:bg-white/20 overflow-hidden shrink-0">
            <img :src="`/storage/img/${playlist.id}.png`" class="w-full h-full object-cover" />
        </div>
        <div class="min-w-0 grow">
            <p class="text-sm font-medium truncate">{{ playlist.name }}</p>
            <p class="text-xs opacity-60 truncate">
                {{ playlist.songs_count }} {{ $t('sidebar.playlistNumber') }}
            </p>
        </div>
        <div v-if="playlist.id !== '00000000-0000-0000-0000-000000000000'" class="relative" ref="dropdownRef" @click="e => e.stopImmediatePropagation()">
            <Button @click="dropdownOpen = !dropdownOpen" class="group transition-all duration-150">
                <Icon name="dots-vertical" class="size-5 transition-transform duration-150 group-hover:scale-110"></Icon>
            </Button>
            <ul v-if="dropdownOpen" class="absolute mt-2 bg-white dark:bg-black border border-gray-200 dark:border-gray-500/6 rounded-md shadow-lg py-1 z-50 right-0">
                <li @click="selectMenu('delete')" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer whitespace-nowrap">
                    <span>{{ $t('sidebar.deletePlaylistButton') }}</span>
                    <Icon name="trash" class="size-5 text-red-500" />
                </li>
                <!-- TODO: rename playlist -->
            </ul>
        </div>
    </div>
</template>
