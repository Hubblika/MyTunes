import { defineStore } from "pinia";
import type { _Song } from "@/types";
import axios from "axios";
import type { _Playlist } from "@/types";

export const usePlayerStore = defineStore("player", {
    state: () => ({
        queue: [] as _Song[],
        currentIndex: 0,
        isPlaying: false,
        shuffle: false,
        loop: false,
        liked: false,
        time: 0,
        duration: 0,
        volume: 10,
        originalQueue: [] as _Song[],
        currentPlaylist: null as string | null,
        playlists: new Map<string, _Playlist>(),
    }),

    getters: {
        currentTrack: (state) => state.queue[state.currentIndex],
        audioSrc: (state) => state.queue[state.currentIndex]?.url ?? "",
        uuid: (state) => state.queue[state.currentIndex]?.uuid,
        _liked: (state) => state.liked,
        currentplaylist: (state) => state.currentPlaylist,
    },

    actions: {
        async playSong(song: _Song) {
            const current = this.currentTrack;

            if (!current || current.uuid !== song.uuid) {
                if (this.queue.length === 0) {
                    this.queue = [song];
                } else {
                    this.queue.splice(this.currentIndex, 1);
                    this.queue.unshift(song);
                }
                this.currentIndex = 0;
                this.isPlaying = true;
                await this.fetchLiked();
            }
        },

        async togglePlay() {
            this.isPlaying = !this.isPlaying;
            await this.fetchLiked();
        },

        addToQueue(song: _Song, playlist: string) {
            this.queue.push(song);
            this.currentPlaylist = playlist;
        },

        next() {
            if (this.currentIndex < this.queue.length - 1) {
                this.currentIndex++;
                return;
            } else if (!this.loop) {
                this.isPlaying = false;
            }
            this.currentIndex = 0;
        },

        previous() {
            if (this.currentIndex > 0) {
                this.currentIndex--;
            }
        },

        emptyQueue() {
            this.queue = [];
        },

        shuffleQueue() {
            this.originalQueue = [...this.queue];
            const currentTrack = this.queue[this.currentIndex];

            for (let i = this.queue.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [this.queue[i], this.queue[j]] = [this.queue[j], this.queue[i]];
            }

            this.currentIndex = this.queue.findIndex(
                (song) => song === currentTrack,
            );
        },

        sortQueue() {
            const currentTrack = this.queue[this.currentIndex];

            this.queue = [...this.originalQueue];

            this.currentIndex = this.queue.findIndex(
                (song) => song === currentTrack,
            );
        },

        async toggleLike() {
            if (!this.currentTrack) return;

            this.liked = !this.liked;

            try {
                if (this.liked) {
                    await axios.post(`/api/like/${this.currentTrack.uuid}`);
                } else {
                    await axios.delete(`/api/like/${this.currentTrack.uuid}`);
                }
            } catch (err) {
                console.error("Failed to toggle like:", err);
                this.liked = !this.liked;
            }
        },

        async fetchLiked() {
            if (!this.currentTrack) {
                this.liked = false;
                return;
            }

            try {
                const res = await axios.get(
                    `/api/like/${this.currentTrack.uuid}`,
                );
                this.liked = res.data.liked;
            } catch (err) {
                console.error("Failed to fetch liked status:", err);
                this.liked = false;
            }
        },

        setPlaylist(playlist: _Playlist) {
            this.playlists.set(playlist.uuid, playlist);
        },

        renamePlaylist(uuid: string, name: string) {
            const pl = this.playlists.get(uuid);
            if (pl) pl.name = name;
        },

        deletePlaylist(uuid: string) {
            this.playlists.delete(uuid);

            if (this.currentPlaylist === uuid) {
                this.emptyQueue();
                this.currentPlaylist = null;
                this.currentIndex = 0;
                this.isPlaying = false;
            }
        },
    },
});
