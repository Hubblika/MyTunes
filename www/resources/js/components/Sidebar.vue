<script setup lang="ts">
import { onMounted, ref, computed } from 'vue';
import { Searchbar, PlaylistCard, Button, Icon } from './common';
import { usePage, router } from '@inertiajs/vue3';
import { usePlayerStore } from '@/stores/player';
import { _Playlist } from '@/types';

const page = usePage();
const player = usePlayerStore();

const playlists = ref<_Playlist[]>([]);
const searchQuery = ref('');

const filteredPlaylists = computed(() => {
    if (!searchQuery.value) return playlists.value;

    const query = searchQuery.value.toLowerCase();
    return playlists.value.filter(p =>
        p.name.toLowerCase().startsWith(query)
    );
});

async function getOwnPlaylists() {
    playlists.value = await player.fetchPlaylists(page.props.self.username);
}

async function addPlaylist() {
    const newPl = await player.addPlaylist('New Playlist');
    if (newPl) playlists.value.push(newPl);
}

async function deletePlaylist(uuid: string) {
    const success = await player.deletePlaylistAPI(uuid);
    if (!success) return;

    playlists.value = playlists.value.filter(p => p.uuid !== uuid);

    if (page.props.uuid === uuid) router.visit('/');
}

async function renamePlaylist(uuid: string, name: string) {
    const updated = await player.renamePlaylistAPI(uuid, name);
    if (!updated) return;

    playlists.value = playlists.value.map(p =>
        p.uuid === uuid ? updated : p
    );
}

onMounted(getOwnPlaylists);
</script>

<template>
    <aside class="w-96 flex flex-col min-h-full max-h-full
              rounded-2xl border border-black/10 dark:border-white/10
              bg-white/20 dark:bg-white/5 backdrop-blur-xl
              shadow-xl dark:shadow-2xl">
        <div class="flex items-center justify-between h-16 shrink-0 px-4">
            <h1 class="text-lg font-bold pl-2">{{ $t('sidebar.title') }}</h1>

            <Button @click="addPlaylist" :tooltip="$t('tooltip.addPlaylist')" class="group relative">
                <Icon name="circle-plus"
                    class="w-5 h-5 transition-all duration-200 group-hover:text-black/60 dark:group-hover:text-white/80" />
            </Button>
        </div>

        <div class="px-4">
            <Searchbar class="w-full shrink-0 mb-4" :placeholder="$t('sidebar.searchbar')" v-model="searchQuery" />
        </div>

        <div class="space-y-1 pt-3 flex-1 overflow-y-auto overflow-x-hidden custom-scrollbar px-4">
            <PlaylistCard
                v-if="!searchQuery || $t('sidebar.likedSongs').toLowerCase().startsWith(searchQuery.toLowerCase())"
                :playlist="{
                    uuid: '00000000-0000-0000-0000-000000000000',
                    user_id: page.props.self.id,
                    name: $t('sidebar.likedSongs'),
                    description: '',
                    public: false,
                    created_at: new Date().toISOString(),
                    updated_at: new Date().toISOString(),
                }" />

            <PlaylistCard v-for="pl in filteredPlaylists" :key="pl.uuid" :playlist="player.playlists.get(pl.uuid)!"
                @delete-playlist="deletePlaylist" @rename-playlist="renamePlaylist" />
        </div>
    </aside>
</template>
