<script setup lang="ts">
import { Searchbar, PlaylistCard, Button, Icon } from '../components/common';
import Maincontent from '@/components/Maincontent.vue';
import { usePage, router } from '@inertiajs/vue3';
import { usePlayerStore } from '@/stores/player';
import { onMounted, ref, computed } from 'vue';
import { MainLayout } from '@/layouts';
import { _Playlist } from '@/types';
import { useI18n } from 'vue-i18n';

const page = usePage();
const player = usePlayerStore();
const { t } = useI18n();

defineOptions({
    layout: MainLayout
});

const playlists = ref<_Playlist[]>([]);
const searchQuery = ref('');

const filteredPlaylists = computed(() => {
    if (!searchQuery.value) return playlists.value;

    const query = searchQuery.value.toLowerCase();
    return playlists.value.filter(p =>
        p.name.toLowerCase().startsWith(query)
    );
});

onMounted(getOwnPlaylists);

async function getOwnPlaylists() {
    playlists.value = await player.fetchPlaylists(page.props.self.username);
}

async function addPlaylist() {
    const newPl = await player.addPlaylist(t('sidebar.newPlaylistDefaultName'));
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
</script>

<template>
    <Maincontent>
        <div class="w-full flex flex-col h-full min-h-screen p-4">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-2xl font-bold">{{ t('sidebar.title') }}</h1>
                <Button @click="addPlaylist" :tooltip="t('tooltip.addPlaylist')" class="group relative">
                    <Icon name="circle-plus"
                        class="w-6 h-6 transition-all duration-200 group-hover:text-black/60 dark:group-hover:text-white/80" />
                </Button>
            </div>

            <div class="mb-4">
                <Searchbar v-model="searchQuery" :placeholder="t('sidebar.searchbar')" class="w-full" />
            </div>

            <div class="flex-1 space-y-2 w-full pb-40">
                <PlaylistCard
                    v-if="!searchQuery || t('sidebar.likedSongs').toLowerCase().startsWith(searchQuery.toLowerCase())"
                    :playlist="{
                        uuid: '00000000-0000-0000-0000-000000000000',
                        user_id: page.props.self.id,
                        name: t('sidebar.likedSongs'),
                        description: '',
                        public: false,
                        created_at: new Date().toISOString(),
                        updated_at: new Date().toISOString(),
                    }" />

                <PlaylistCard v-for="pl in filteredPlaylists" :key="pl.uuid" :playlist="player.playlists.get(pl.uuid)!"
                    @delete-playlist="deletePlaylist" @rename-playlist="renamePlaylist" />
            </div>
        </div>
    </Maincontent>
</template>
