<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount, nextTick, computed } from 'vue';
import { Icon, Button, PlaylistEditModal } from '.';
import { usePlayerStore } from '@/stores/player';
import { router } from '@inertiajs/vue3';
import { _Playlist } from '@/types';

const player = usePlayerStore();

const { playlist } = defineProps<{ playlist: _Playlist }>();
const emit = defineEmits<{
    deletePlaylist: [id: string]
    renamePlaylist: [id: string, name: string]
}>();

const dropdownRef = ref<HTMLElement | null>(null);
const dropdownOpen = ref(false);
const menuX = ref(0);
const menuY = ref(0);
const OFFSET = 6;

const renamingModal = ref(false);
const renameInput = ref('');
const coverFile = ref<File | null>(null);
const DISABLED_UUID = '00000000-0000-0000-0000-000000000000';
const isMobile = ref(false);

onMounted(async () => {
    window.addEventListener('playlist-context-open', handleOtherDropdown);
    window.addEventListener('click', handleClickOutside);
    checkMobile();
    window.addEventListener('resize', checkMobile);
    await player.fetchLikedSongs();
    await player.fetchPlaylistSongs(playlist.uuid);
});

onBeforeUnmount(() => {
    window.removeEventListener('playlist-context-open', handleOtherDropdown);
    window.removeEventListener('click', handleClickOutside);
    window.removeEventListener('resize', checkMobile);
});

const coverUrl = computed(() => {
    const pl = player.playlists.get(playlist.uuid);
    return pl?.cover_url || '/uploads/thumbnails/defaultThumbnail.png';
})

function checkMobile() {
    isMobile.value = window.innerWidth <= 768;
}

async function play() {
    const isLikedPlaylist = playlist.uuid === DISABLED_UUID;
    if (isLikedPlaylist) {
        if (!player.likedSongList.length) return;
        if (player.currentPlaylist === playlist.uuid) player.togglePlay();
        else player.addPlaylistToQueue({ ...playlist, songs: [...player.likedSongList] });
    } else {
        if (player.currentPlaylist === playlist.uuid) player.togglePlay();
        else player.addPlaylistToQueue(playlist);
    }
}

const selectMenu = (item: 'rename' | 'delete') => {
    dropdownOpen.value = false;
    if (item === 'delete') emit('deletePlaylist', playlist.uuid);
    if (item === 'rename') {
        renameInput.value = playlist.name;
        renamingModal.value = true;
    }
}

function openDropdown(e: MouseEvent) {
    if (playlist.uuid === DISABLED_UUID) return;

    e.preventDefault();
    e.stopPropagation();

    if (isMobile.value) {
        dropdownOpen.value = true;
        return;
    }

    menuX.value = e.clientX + OFFSET;
    menuY.value = e.clientY + OFFSET;
    dropdownOpen.value = true;

    nextTick(() => {
        if (!dropdownRef.value) return;
        const { innerWidth, innerHeight } = window;
        const rect = dropdownRef.value.getBoundingClientRect();
        if (menuX.value + rect.width > innerWidth) menuX.value = e.clientX - rect.width - OFFSET;
        if (menuY.value + rect.height > innerHeight) menuY.value = e.clientY - rect.height - OFFSET;
        menuX.value = Math.max(OFFSET, menuX.value);
        menuY.value = Math.max(OFFSET, menuY.value);
    })

    window.dispatchEvent(new CustomEvent('playlist-context-open', { detail: playlist.uuid }));
}

function handleOtherDropdown(e: Event) {
    const event = e as CustomEvent<string>;
    if (event.detail !== playlist.uuid) dropdownOpen.value = false;
}

function handleClickOutside() {
    dropdownOpen.value = false;
}
</script>

<template>
    <div @contextmenu.prevent="openDropdown" :class="player.currentPlaylist === playlist.uuid && player.isPlaying
        ? 'bg-gray-500/10 dark:bg-white/10' : ''"
        class="flex items-center gap-3 p-2 rounded-md hover:bg-gray-500/10 dark:hover:bg-white/10 transition">
        <div class="relative size-12 rounded bg-gray-400/30 dark:bg-white/20 overflow-hidden shrink-0 group cursor-pointer"
            @click="() => router.visit(`/playlist/${playlist.uuid}`)">
            <img :src="coverUrl"
                class="w-full h-full object-cover transition-opacity duration-150 group-hover:opacity-50" />
            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-150"
                @click.stop="play">
                <Icon
                    :name="player.currentPlaylist === playlist.uuid && player.isPlaying ? 'player-pause-filled' : 'player-play-filled'"
                    class="size-6 text-white hover:scale-110 transition-transform" />
            </div>
        </div>

        <div class="min-w-0 flex flex-col grow cursor-pointer"
            @click="() => router.visit(`/playlist/${playlist.uuid}`)">
            <span class="text-sm font-medium truncate">{{ playlist.name }}</span>
            <span class="text-xs opacity-60 truncate">{{ playlist.uuid === DISABLED_UUID ? player.likedCount :
                playlist.songs_count ?? 0 }} {{ $t('sidebar.playlistNumber') }}</span>
        </div>

        <Button @click.stop="openDropdown($event)" :tooltip="$t('tooltip.moreOptions')"
            v-if="playlist.uuid !== DISABLED_UUID" class="p-2 rounded-full">
            <Icon name="dots-vertical" class="size-5 transition-transform duration-150 group-hover:scale-110" />
        </Button>
    </div>

    <Teleport to="body">
        <ul ref="dropdownRef" v-if="dropdownOpen && !isMobile" :style="{ left: `${menuX}px`, top: `${menuY}px` }"
            class="fixed text-black dark:text-white bg-white/20 dark:bg-black/20 backdrop-blur-md border border-black/10 dark:border-white/10 rounded-2xl shadow-lg py-2 z-50 min-w-[200px] whitespace-nowrap"
            @click.stop>
            <li @click="selectMenu('rename')"
                class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-white/30 dark:hover:bg-black/30 cursor-pointer">
                <Icon name="pencil" class="size-5" />
                <span>{{ $t('sidebar.editPlaylistButton') }}</span>
            </li>
            <li @click="selectMenu('delete')"
                class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-white/30 dark:hover:bg-black/30 cursor-pointer">
                <Icon name="trash" class="size-5 text-red-500" />
                <span>{{ $t('sidebar.deletePlaylistButton') }}</span>
            </li>
        </ul>
    </Teleport>

    <Teleport to="body">
        <transition name="slide-up">
            <div v-if="dropdownOpen && isMobile" @click.self="dropdownOpen = false"
                class="fixed inset-0 bg-black/40 z-50 flex justify-center items-end">
                <div
                    class="bg-white/20 dark:bg-black/20 backdrop-blur-xl border border-white/10 dark:border-black/10 rounded-t-3xl w-full max-h-[80vh] overflow-auto p-4">
                    <li @click="selectMenu('rename')"
                        class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-white/30 dark:hover:bg-black/30 cursor-pointer">
                        <Icon name="pencil" class="size-5" />
                        <span>{{ $t('sidebar.editPlaylistButton') }}</span>
                    </li>
                    <li @click="selectMenu('delete')"
                        class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-white/30 dark:hover:bg-black/30 cursor-pointer">
                        <Icon name="trash" class="size-5 text-red-500" />
                        <span>{{ $t('sidebar.deletePlaylistButton') }}</span>
                    </li>
                </div>
            </div>
        </transition>

         <PlaylistEditModal v-model="renamingModal" :title="$t('modal.title')" :name-value="renameInput"
        :description-value="playlist?.description || ''" :cover-url="playlist?.cover_url || ''"
        @update:nameValue="renameInput = $event" @update:descriptionValue="playlist!.description = $event"
        @update:coverFile="coverFile = $event" @save="async () => {
            if (!playlist) return;
            await player.updatePlaylist(playlist.uuid, {
                name: renameInput,
                description: playlist.description ?? undefined,
                cover: coverFile ?? undefined
            });
            renamingModal = false;
            coverFile = null;
        }" />
    </Teleport>
</template>