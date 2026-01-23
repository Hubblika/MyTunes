<script setup lang="ts">
import { onMounted, ref, computed } from 'vue';
import axios from 'axios';
import { Searchbar, PlaylistCard, Button, Icon } from './common';
import playlist from '@/actions/App/Http/Controllers/PlaylistController';
import { usePage, router } from '@inertiajs/vue3';
import { usePlayerStore } from '@/stores/player'
import { _Playlist } from '@/types';

const page = usePage();

const playlists = ref<_Playlist[]>([]);
const searchQuery = ref('');

const player = usePlayerStore()

const filteredPlaylists = computed(() => {
    if (!searchQuery.value) return playlists.value;

    const query = searchQuery.value.toLowerCase();
    return playlists.value.filter(p =>
        p.name.toLowerCase().startsWith(query)
    );
});

async function getOwnPlaylists() {
    const { data } = await axios.get(
        playlist.index.url() + `?username=${page.props.self.username}`,
        { headers: { Accept: 'application/json' } }
    );

    playlists.value = data.data;

    data.data.forEach((pl: _Playlist) => player.setPlaylist(pl))
}

async function addPlaylist() {
    try {
        const { data } = await axios.post(
            playlist.store.url(),
            { name: 'New Playlist' },
            { headers: { Accept: 'application/json' } }
        );

        const newPlaylist = data.data as _Playlist;

        playlists.value.push(newPlaylist);

        player.setPlaylist(newPlaylist);

        console.log(`Playlist "${newPlaylist.name}" added successfully`);
    } catch (err) {
        console.error('Failed to add playlist', err);
    }
}

async function deletePlaylist(playlistId: string) {
    try {
        await axios.delete(playlist.destroy.url(playlistId), {
            headers: { Accept: 'application/json' }
        });

        playlists.value = playlists.value.filter(p => p.uuid !== playlistId);

        player.deletePlaylist(playlistId);

        if (page.props.uuid === playlistId) {
            router.visit('/');
        }

        console.log('Playlist deleted successfully');
    } catch (err) {
        console.error('Failed to delete playlist', err);
    }
}

async function renamePlaylist(playlistId: string, name: string) {
    const { data } = await axios.put(
        playlist.update.url(playlistId),
        { name },
        { headers: { Accept: 'application/json' } }
    );

    playlists.value = playlists.value.map(p =>
        p.uuid === playlistId ? data.data : p
    );


    player.renamePlaylist(playlistId, data.data.name)
}

onMounted(getOwnPlaylists);
</script>

<template>
    <aside class="bg-gray-500/6 dark:text-white w-96 flex flex-col min-h-full max-h-full rounded-lg">
        <div class="flex items-center justify-between h-16 shrink-0 px-4">
            <h1 class="text-lg font-bold pl-2">
                {{ $t('sidebar.title') }}
            </h1>

            <Button @click="addPlaylist" class="group relative">
                <Icon name="circle-plus"
                    class="w-5 h-5 transition-all duration-200 group-hover:text-black/60 dark:group-hover:text-white/80" />
            </Button>
        </div>

        <div class="px-4">
            <Searchbar class="w-full shrink-0 mb-4" :placeholder="$t('sidebar.searchbar')" v-model="searchQuery" />
        </div>

        <div class="space-y-1 pt-3 flex-1 overflow-y-auto custom-scrollbar px-4">
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
