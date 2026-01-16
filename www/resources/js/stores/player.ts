import { defineStore } from "pinia";
import type { _Song } from "@/types";
import axios from "axios";

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
    }),

    getters: {
        currentTrack: (state) => state.queue[state.currentIndex],
        audioSrc: (state) => state.queue[state.currentIndex]?.url ?? "",
        uuid: (state) => state.queue[state.currentIndex]?.uuid,
        _liked: (state) => state.liked,
    },

    actions: {
        async playSong(song: _Song) {
            if (this.isPlaying) {
                this.togglePlay();
                await new Promise((resolve) => setTimeout(resolve, 50));
                this.emptyQueue();
                this.queue = [song];
                this.currentIndex = 0;
                this.togglePlay();
            } else {
                this.queue = [song];
                this.currentIndex = 0;
                this.togglePlay();
            }

            await this.fetchLiked();
        },

        togglePlay() {
            this.isPlaying = !this.isPlaying;
        },

        addToQueue(song: _Song) {
            this.queue.push(song);
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
    },
});
