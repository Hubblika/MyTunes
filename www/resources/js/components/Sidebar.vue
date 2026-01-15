<script setup lang="ts">
import { onMounted, ref } from 'vue';

import { Searchbar, PlaylistCard, Button, Icon } from './common';
import playlist from '@/actions/App/Http/Controllers/PlaylistController';
import { usePage } from '@inertiajs/vue3';
import { Playlist } from '@/lib/types';

const page = usePage();

const playlists = ref<Playlist[]>([]);

async function getOwnPlaylists() {
    const { data } = await axios.get(playlist.index.url() + `?username=${page.props.self.username}`, {
        headers: { Accept: 'application/json' }
    });

    playlists.value = data.data;
}

async function addPlaylist() {
    const { data } = await axios.post(playlist.store.url(), {
        name: 'New Playlist'
    }, {
        headers: { Accept: 'application/json' }
    });

    console.log(data);
    playlists.value.push(data.data);
}

async function deletePlaylist(playlistId: string) {
    // TODO: add "are you sure?" prompt
    const { data } = await axios.delete(playlist.destroy.url(playlistId), {
        headers: { Accept: 'application/json' }
    });

    playlists.value = playlists.value.filter(item => item.id !== playlistId);
}

async function renamePlaylist(playlistId: string, name: string) {
    const { data } = await axios.put(playlist.update.url(playlistId), { name }, {
        headers: { Accept: 'application/json' }
    });

    playlists.value = playlists.value.map(playlist => playlist.id === playlistId ? data.data : playlist);
}

onMounted(async () => {
    await getOwnPlaylists();
});
</script>

<template>
    <aside class="bg-gray-500/6 dark:text-white w-96 flex flex-col min-h-full max-h-full px-4 rounded-lg">
        <div class="flex items-center justify-between h-16 shrink-0">
            <h1 class="text-lg font-bold pl-2">{{ $t('sidebar.title')}}</h1>
            <Button @click="addPlaylist" class="group relative">
                <Icon name="circle-plus" class="w-5 h-5 transition-all duration-200 group-hover:text-black/60 dark:group-hover:text-white/80"></Icon>
            </Button>
        </div>
        <!-- TODO add instant playlist filtering -->
        <Searchbar class="w-full shrink-0" :placeholder="$t('sidebar.searchbar')"></Searchbar>
        <div class="space-y-1 pt-3 max-h-full flex-1 overflow-y-auto">
            <PlaylistCard
                :playlist="{
                    id: '00000000-0000-0000-0000-000000000000',
                    user_id: page.props.self.id,
                    name: $t('sidebar.likedSongs'),
                    description: '',
                    public: false,
                    is_album: false,
                    created_at: new Date().toISOString(),
                    updated_at: new Date().toISOString(),

                    songs_count: 100
                }"
            />
            <PlaylistCard
                v-for="playlist in playlists"
                :playlist="playlist"
                @delete-playlist="deletePlaylist"
                @rename-playlist="renamePlaylist"
            />
        </div>
    </aside>
</template>
