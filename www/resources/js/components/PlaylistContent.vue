<script setup lang="ts">
import { ref, onMounted } from "vue";
import axios from "axios";
import { PlaylistSong } from "./common";
import { _Song } from "@/types";

const likes = ref<string[]>([]);
const likedSongs = ref<_Song[]>([]);


async function fetchLikes() {
    const response = await axios.get("/api/like");
    likes.value = response.data.likes;
}

async function fetchLikedSongs() {
    if (!likes.value?.length) return;

    try {
        const requests = likes.value.map(uuid => axios.get(`/api/songs/${uuid}`));
        const responses = await Promise.all(requests);

        likedSongs.value = responses
            .map(r => r.data.data)
            .filter(Boolean);

    } catch (err) {
        console.error("Error fetching liked songs:", err);
        likedSongs.value = [];
    }
}

onMounted(async () => {
    await fetchLikes();
    await fetchLikedSongs();
    console.log(likedSongs.value[0].title)
});
</script>

<template>
    <section class="flex flex-col h-full gap-4">
        <header class="px-4">
            <h1 class="text-2xl font-bold text-black dark:text-white">
                    asd
            </h1>
        </header>

        <div class="grid grid-cols-[32px_48px_1fr_1fr_120px_100px] gap-4 px-4 py-2
             text-xs uppercase tracking-widest
             text-black border-black dark:text-neutral-400
             border-b dark:border-neutral-800">
            <div class="text-right">#</div>
            <div></div>
            <div>{{ $t("playlist.headerTitle") }}</div>
            <div>{{ $t("playlist.headerAlbum") }}</div>
            <div>{{ $t("playlist.headerAddedAt") }}</div>
            <div class="justify-self-end">{{ $t("playlist.headerDuration") }}</div>
        </div>

        <div class="flex flex-col overflow-y-auto">
            <PlaylistSong v-for="(song, index) in likedSongs" :key="song.uuid" :index="index + 1" :song="song"/>
        </div>
    </section>
</template>
