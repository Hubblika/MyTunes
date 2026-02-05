<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount, computed } from "vue";
import { PlaylistSong, Icon, RenameModal } from "./common";
import { _Playlist } from "@/types";
import { usePlayerStore } from "@/stores/player";
import { router } from "@inertiajs/vue3";

const props = defineProps<{ uuid: string }>();
const player = usePlayerStore();

const playlist = computed(() => {
    return player.playlists.get(props.uuid) ?? null;
});

const dropdownRef = ref<HTMLElement | null>(null);
const dropdownOpen = ref(false);

const renamingModal = ref(false);
const renameInput = ref("");

const handleClickOutside = (event: MouseEvent) => {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target as Node)) {
        dropdownOpen.value = false;
    }
};

async function play() {
    const isLikedPlaylist = props.uuid === "00000000-0000-0000-0000-000000000000";

    if (isLikedPlaylist) {
        if (!player.likedSongList.length) return;

        if (player.currentPlaylist === props.uuid) {
            player.togglePlay();
        } else {
            const likedPlaylist: _Playlist = {
                uuid: props.uuid,
                user_id: "system",
                name: "Liked Songs",
                public: false,
                created_at: new Date().toISOString(),
                updated_at: new Date().toISOString(),
                songs: [...player.likedSongList],
            };
            player.addPlaylistToQueue(likedPlaylist);
        }
    } else if (playlist.value) {
        if (player.currentPlaylist === props.uuid) {
            player.togglePlay();
        } else {
            player.addPlaylistToQueue(playlist.value);
        }
    }
}

async function confirmRename() {
    if (!renameInput.value.trim() || !playlist.value) return;
    const updated = await player.renamePlaylistAPI(props.uuid, renameInput.value.trim());
    if (updated) playlist.value.name = updated.name;
}

async function deleteCurrentPlaylist() {
    if (!playlist.value) return;
    const success = await player.deletePlaylistAPI(playlist.value.uuid);
    if (success) router.visit("/");
}

onMounted(async () => {
    document.addEventListener("click", handleClickOutside);

    if (props.uuid === "00000000-0000-0000-0000-000000000000") {
        await player.fetchLikedSongs();
    } else {
        await player.fetchPlaylist(props.uuid);
        await player.fetchPlaylistSongs(props.uuid);
    }
});

onBeforeUnmount(() => {
    document.removeEventListener("click", handleClickOutside);
});
</script>

<template>
    <section class="flex flex-col h-full gap-4">
        <header class="px-4 flex flex-col md:flex-row items-start md:items-end gap-4 md:gap-6">
            <img src="/uploads/thumbnails/defaultThumbnail.png" alt="Playlist Cover"
                class="w-32 h-32 rounded-lg object-cover shadow-lg" />

            <div class="flex flex-col justify-end flex-1">
                <h1 class="text-4xl font-bold text-black dark:text-white">
                    {{ playlist?.name ?? $t('playlist.likedTitle') }}
                </h1>
                <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                    {{ props.uuid === '00000000-0000-0000-0000-000000000000' ? player.likedCount : playlist?.songs_count ?? 0 }} {{ $t('playlist.likedNumber') }}
                </p>
            </div>
        </header>

        <div class="px-4 flex items-center justify-between">
            <button @click="play" class="flex h-16 w-16 items-center justify-center rounded-full bg-cyan-500 text-white shadow-lg
               hover:scale-105 active:scale-95 transition-transform duration-150">
                <Icon :name="player.currentPlaylist === props.uuid && player.isPlaying
                    ? 'player-pause-filled'
                    : 'player-play-filled'" class="size-8 text-white" />
            </button>

            <div v-if="props.uuid !== '00000000-0000-0000-0000-000000000000'" class="relative" ref="dropdownRef">
                <button @click="dropdownOpen = !dropdownOpen"
                    class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700">
                    <Icon name="dots-vertical" class="size-5 transition-transform duration-150 group-hover:scale-110" />
                </button>

                <ul v-if="dropdownOpen"
                    class="absolute mt-2 bg-white dark:bg-black border border-gray-200 dark:border-gray-500/6 rounded-md shadow-lg py-1 z-50 right-0">
                    <li @click="
                        renameInput = playlist?.name ?? '';
                    renamingModal = true;
                    dropdownOpen = false;
                    "
                        class="flex items-center gap-3 px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer whitespace-nowrap">
                        <Icon name="pencil" class="size-5 text-black dark:text-white" />
                        <span>{{ $t('sidebar.renamePlaylistButton') }}</span>
                    </li>
                    <li class="flex items-center gap-3 px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer whitespace-nowrap"
                        @click="deleteCurrentPlaylist">
                        <Icon name="trash" class="size-5 text-red-500" />
                        <span>{{ $t('sidebar.deletePlaylistButton') }}</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="grid grid-cols-[32px_48px_1fr_1fr_180px_100px_20px] gap-4 px-4 py-2
         text-xs uppercase tracking-widest
         text-black border-black dark:text-neutral-400
         border-b dark:border-neutral-800">
            <div class="text-right">#</div>
            <div></div>
            <div>{{ $t("playlist.headerTitle") }}</div>
            <div>{{ $t("playlist.headerAlbum") }}</div>
            <div>{{ $t("playlist.headerAddedAt") }}</div>
            <div class="justify-self-end">{{ $t("playlist.headerDuration") }}</div>
            <div class="justify-self-end"></div>
        </div>

        <div class="flex flex-col overflow-y-auto">
            <template v-if="props.uuid === '00000000-0000-0000-0000-000000000000'">
                <PlaylistSong v-for="(song, index) in player.likedSongList" :key="song.uuid" :index="index + 1"
                    :song="song" :playlistUuid="props.uuid" />
            </template>
            <template v-else>
                <PlaylistSong v-for="(song, index) in playlist?.songs ?? []" :key="song.uuid" :index="index + 1"
                    :song="song" :playlistUuid="props.uuid" />
            </template>
        </div>
    </section>

    <RenameModal v-model="renamingModal" v-model:inputValue="renameInput" :title="$t('sidebar.renamePlaylistButton')"
        @save="confirmRename" />
</template>
