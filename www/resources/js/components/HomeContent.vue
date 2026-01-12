<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { HomeRow } from './common';
import axios from 'axios';
import { _Song } from '@/types';


const songs = ref<_Song[]>([]);

const getSongs = async () => {
    try {
        const response = await axios.get('/api/songs');
        songs.value = response.data.data;
        console.log(response.data.data);
    } catch (error) {
        console.error(error);
    }
};

onMounted(() => {
    getSongs();
});


const songsByArtist = computed(() => {
    const grouped: Record<string, _Song[]> = {};

    for (const song of songs.value) {
        if (!grouped[song.artist]) {
            grouped[song.artist] = [];
        }
        grouped[song.artist].push(song);
    }

    return grouped;
});
</script>


<template>
    <div class="p-6 flex flex-col gap-4">
        <HomeRow v-for="(artistSongs, artist) in songsByArtist" :key="artist" :title="artist" :songs="artistSongs"/>
    </div>
</template>