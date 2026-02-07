<script setup lang="ts">
import { onMounted, ref, watch } from 'vue';
import { MainContent } from '@/components';
import { MainLayout } from '@/layouts';
import { HomeRow } from '@/components/common';
import _ from 'lodash';

const { query } = defineProps<{ query: string }>();

const songs = ref([]);

async function search(q: string) {
    if (!q) {
        songs.value = [];
        return;
    }

    const res = await fetch(`/songs?q=${encodeURIComponent(q)}`, {
        headers: { Accept: 'application/json' }
    });
    const data = await res.json();
    songs.value = data.data;
}

const debouncedSearch = _.debounce(search, 300);

watch(() => query, (q) => {
    debouncedSearch(q);
}, { immediate: true });

function groupByArtist(songs: any[]) {
    const grouped: Record<string, any[]> = {};
    for (const song of songs) {
        if (!grouped[song.artist]) grouped[song.artist] = [];
        grouped[song.artist].push(song);
    }
    return grouped;
}
</script>

<template>
    <MainLayout>
        <MainContent>
            <div v-if="songs.length === 0" class="p-6 text-gray-500 dark:text-gray-400">
                No songs found.
            </div>

            <div v-else class="p-6 flex flex-col gap-4">
                <HomeRow
                    v-for="(artistSongs, artist) in groupByArtist(songs)"
                    :key="artist"
                    :title="artist"
                    :songs="artistSongs"
                />
            </div>
        </MainContent>
    </MainLayout>
</template>
