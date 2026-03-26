<script setup lang="ts">
import { HomeRow, Searchbar } from '@/components/common';
import { MainContent } from '@/components';
import { MainLayout } from '@/layouts';
import { ref, watch } from 'vue';
import _ from 'lodash';

defineOptions({
    layout: MainLayout
})

const { query } = defineProps<{ query: string }>();

const songs = ref<any[]>([]);

const isMobile = window.matchMedia('(max-width: 1023px)').matches;

async function fetchAll() {
    const res = await fetch('/songs', {
        headers: { Accept: 'application/json' }
    });

    const data = await res.json();
    songs.value = data.data;
}

async function search(q: string) {
    if (!q) {
        if (isMobile) {
            await fetchAll();
        } else {
            songs.value = [];
        }
        return;
    }

    const res = await fetch(`/songs?q=${encodeURIComponent(q)}`, {
        headers: { Accept: 'application/json' }
    });

    const data = await res.json();
    songs.value = data.data;
}

const debouncedSearch = _.debounce(search, 300);

const mobileQuery = ref(query ?? '');

watch(mobileQuery, (q) => {
    debouncedSearch(q);
});

watch(() => query, (q) => {
    mobileQuery.value = q ?? '';
    debouncedSearch(q);
}, { immediate: true })

function groupByArtist(songs: any[]) {
    const grouped: Record<string, any[]> = {}

    for (const song of songs) {
        if (!grouped[song.artist]) grouped[song.artist] = [];
        grouped[song.artist].push(song);
    }

    return grouped;
}
</script>

<template>
    <MainContent>
        <div class="p-4 lg:hidden sticky top-0 z-10 backdrop-blur bg-white/30 dark:bg-black/30">
            <Searchbar v-model="mobileQuery" :placeholder="$t('header.searchbar')" class="w-full" />
        </div>

        <div v-if="songs.length === 0" class="p-6 text-gray-500 dark:text-gray-400">
            {{ $t('search.noResult') }}
        </div>

        <div v-else class="p-6 flex flex-col gap-4">
            <HomeRow v-for="(artistSongs, artist) in groupByArtist(songs)" :key="artist" :title="artist"
                :songs="artistSongs" />
        </div>
    </MainContent>
</template>
