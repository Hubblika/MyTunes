<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount, computed } from "vue";
import { PlaylistSong, Icon, PlaylistEditModal, Button } from "./common";
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
const coverFile = ref<File | null>(null);
const DISABLED_UUID = '00000000-0000-0000-0000-000000000000'

const canEditPlaylist = computed(() => props.uuid !== DISABLED_UUID)

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
            <div class="relative w-32 h-32 rounded-lg overflow-hidden group"
                :class="canEditPlaylist ? 'cursor-pointer' : 'cursor-default'"
                @click="canEditPlaylist && (renamingModal = true)">
                <img :src="playlist?.cover_url || '/uploads/thumbnails/defaultThumbnail.png'" alt="Playlist Cover"
                    class="w-full h-full object-cover rounded-lg shadow-lg" />

                <Icon v-if="canEditPlaylist" name="pencil" class="size-12 text-white bg-black/50 rounded-full p-1
                   absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2
                   opacity-0 group-hover:opacity-100
                   transition-opacity duration-200
                   pointer-events-none" />
            </div>

            <div class="flex flex-col justify-end flex-1 gap-1">
                <h1 class="text-4xl font-bold text-black dark:text-white select-none"
                    :class="canEditPlaylist ? 'cursor-pointer hover:underline' : 'cursor-default'"
                    @click="canEditPlaylist && (renamingModal = true)">
                    {{ playlist?.name ?? $t('playlist.likedTitle') }}
                </h1>

                <p v-if="playlist?.description" class="text-sm text-neutral-600 dark:text-neutral-300
               line-clamp-2 max-w-2xl">
                    {{ playlist.description }}
                </p>

                <p class="text-sm text-neutral-500 dark:text-neutral-400">
                    {{ props.uuid === '00000000-0000-0000-0000-000000000000'
                        ? player.likedCount
                        : playlist?.songs_count ?? 0 }}
                    {{ $t('playlist.likedNumber') }}
                </p>
            </div>
        </header>

        <div class="px-4 flex items-center justify-between">
            <Button @click="play"
                :tooltip="player.currentPlaylist === props.uuid && player.isPlaying ? $t('tooltip.pause') : $t('tooltip.play')"
                class="flex h-16 w-16 items-center justify-center rounded-full bg-cyan-500 text-white shadow-lg
               hover:scale-105 active:scale-95 transition-transform duration-150">
                <Icon :name="player.currentPlaylist === props.uuid && player.isPlaying
                    ? 'player-pause-filled'
                    : 'player-play-filled'" class="size-8 text-white" />
            </Button>

            <div v-if="props.uuid !== '00000000-0000-0000-0000-000000000000'" class="relative" ref="dropdownRef">
                <Button @click="dropdownOpen = !dropdownOpen" :tooltip="$t('tooltip.moreOptions')"
                    class="p-2 rounded-full">
                    <Icon name="dots-vertical" class="size-5 transition-transform duration-150 group-hover:scale-110" />
                </Button>

                <ul v-if="dropdownOpen" class="absolute right-0 mt-2
                                        text-black dark:text-white
                                        bg-white/20 dark:bg-black/20
                                        backdrop-blur-md
                                        border border-black/10 dark:border-white/10
                                        rounded-2xl shadow-lg py-2 z-50
                                        min-w-[200px] whitespace-nowrap">
                    <li @click="
                        renameInput = playlist?.name ?? '';
                    renamingModal = true;
                    dropdownOpen = false;
                    " class="flex items-center gap-3 px-4 py-2 rounded-lg
                            hover:bg-white/30 dark:hover:bg-black/30
                            cursor-pointer">
                        <Icon name="pencil" class="size-5 text-black dark:text-white" />
                        <span>{{ $t('sidebar.editPlaylistButton') }}</span>
                    </li>
                    <li class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-white/30 dark:hover:bg-black/30 cursor-pointer whitespace-nowrap"
                        @click="deleteCurrentPlaylist">
                        <Icon name="trash" class="size-5 text-red-500" />
                        <span>{{ $t('sidebar.deletePlaylistButton') }}</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="grid grid-cols-[32px_48px_1fr_1fr_180px_100px_40px] gap-4 px-4 py-2
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

        <div class="flex flex-col overflow-y-auto overflow-x-hidden">
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

    <Teleport to="body">
        <PlaylistEditModal v-model="renamingModal" :title="$t('sidebar.editPlaylistButton')" :name-value="renameInput"
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
