// stores/player.ts
import { defineStore } from "pinia";
import type { _Song } from "@/types";

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
    },

    actions: {
        async playSong(song: _Song) {
            if (this.isPlaying) {
                this.togglePlay();
                await new Promise(resolve => setTimeout(resolve, 50));
                this.emptyQueue();
                this.queue = [song];
                this.currentIndex = 0;
                this.togglePlay();
            } else {
                this.queue = [song];
                this.currentIndex = 0;
                this.togglePlay();
            }
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
    },
});
