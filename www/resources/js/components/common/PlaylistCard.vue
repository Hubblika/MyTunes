<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Icon, Button } from '.';
import { router } from '@inertiajs/vue3';
import { Playlist } from '@/lib/types';

const dropdownOpen = ref(false);
const renaming = ref(false);

const { playlist } = defineProps<{
    playlist: Playlist
}>()

const emit = defineEmits<{
    deletePlaylist: [id: string]
    renamePlaylist: [id: string, name: string]
}>();

const selectMenu = (item: string) => {
    dropdownOpen.value = false;

    switch (item) {
        case 'delete':
            emit('deletePlaylist', playlist.uuid);
            break;
        case 'rename':
            if (inputRef.value) {
                inputRef.value.value = playlist.name;
            }
            renaming.value = !renaming.value;
            break;
        default:
            console.log(`Selected ${item}`);
            break;
    }
};

function finishRenaming() {
    renaming.value = false;
    if (inputRef.value?.value) {
        emit('renamePlaylist', playlist.uuid, inputRef.value.value);
    }
}

const dropdownRef = ref<HTMLElement>();
const inputRef = ref<HTMLInputElement>();
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
    <div class="flex items-center gap-3 p-2 rounded-md cursor-pointer hover:bg-gray-500/10 dark:hover:bg-white/10 transition" @click="() => renaming ? {} : router.visit(`/playlist/${playlist.uuid}`)">
        <div class="size-12 rounded bg-gray-400/30 dark:bg-white/20 overflow-hidden shrink-0">
            <img :src="'/uploads/thumbnails/defaultThumbnail.png'" class="w-full h-full object-cover" />
        </div>
        <div class="min-w-0 flex flex-col grow">
            <span v-if="!renaming" class="text-sm font-medium truncate">{{ playlist.name }}</span>
            <input v-else class="text-sm font-medium w-full" :placeholder="playlist.name" @keypress="e => e.key === 'Enter' ? finishRenaming() : {}" ref="inputRef" />
            <span class="text-xs opacity-60 truncate">
                0 {{ $t('sidebar.playlistNumber') }}
            </span>
        </div>
        <div v-if="playlist.uuid !== '00000000-0000-0000-0000-000000000000'" class="relative" ref="dropdownRef" @click="e => e.stopImmediatePropagation()">
            <Button @click="dropdownOpen = !dropdownOpen" class="group transition-all duration-150">
                <Icon name="dots-vertical" class="size-5 transition-transform duration-150 group-hover:scale-110"></Icon>
            </Button>
            <ul v-if="dropdownOpen" class="absolute mt-2 bg-white dark:bg-black border border-gray-200 dark:border-gray-500/6 rounded-md shadow-lg py-1 z-50 right-0">
                <li @click="() => selectMenu('rename')" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer whitespace-nowrap">
                    <Icon :name="renaming ? 'pencil-off' : 'pencil'" class="size-5 text-black" />
                    <span>{{ $t(renaming ? 'generic.cancel' : 'sidebar.renamePlaylistButton') }}</span>
                </li>
                <li @click="() => selectMenu('delete')" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer whitespace-nowrap">
                    <Icon name="trash" class="size-5 text-red-500" />
                    <span>{{ $t('sidebar.deletePlaylistButton') }}</span>
                </li>
            </ul>
        </div>
    </div>
</template>
