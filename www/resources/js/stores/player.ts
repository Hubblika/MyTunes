import { defineStore } from "pinia";
import type { _Song } from "@/types";
import type { _Playlist } from "@/types";
import axios from "axios";

export const usePlayerStore = defineStore("player", {
    state: () => ({
        queue: [] as _Song[],
        currentIndex: 0,
        isPlaying: false,
        shuffle: false,
        loop: false,
        likedSongs: [] as _Song[],
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
        isLiked: (state) => (song?: _Song) =>
            song ? state.likedSongs.some(s => s.uuid === song.uuid) : false,
        likedCount: (state) => state.likedSongs.length,
        likedSongList: (state) => state.likedSongs,
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
            }
        },

        async togglePlay() {
            this.isPlaying = !this.isPlaying;
        },

        addToQueue(song: _Song, playlist: string) {
            this.queue.push(song);
            this.currentPlaylist = playlist;
        },

        next() {
            if (this.currentIndex < this.queue.length - 1) {
                this.currentIndex++;
            } else if (!this.loop) {
                this.isPlaying = false;
            }
            if (this.loop && this.currentIndex >= this.queue.length - 1) {
                this.currentIndex = 0;
            }
        },

        previous() {
            if (this.currentIndex > 0) this.currentIndex--;
        },

        emptyQueue() {
            this.queue = [];
            this.currentIndex = 0;
            this.isPlaying = false;
        },

        shuffleQueue() {
            this.originalQueue = [...this.queue];
            const currentTrack = this.queue[this.currentIndex];

            for (let i = this.queue.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [this.queue[i], this.queue[j]] = [this.queue[j], this.queue[i]];
            }

            this.currentIndex = this.queue.findIndex((song) => song === currentTrack);
        },

        sortQueue() {
            const currentTrack = this.queue[this.currentIndex];
            this.queue = [...this.originalQueue];
            this.currentIndex = this.queue.findIndex((song) => song === currentTrack);
        },

        async toggleLike(song?: _Song) {
            const track = song ?? this.currentTrack;
            if (!track) return;

            const likedIndex = this.likedSongs.findIndex(s => s.uuid === track.uuid);

            try {
                if (likedIndex === -1) {
                    await axios.post(`/api/like/${track.uuid}`);
                    this.likedSongs.push(track);
                } else {
                    await axios.delete(`/api/like/${track.uuid}`);
                    this.likedSongs.splice(likedIndex, 1);
                }
            } catch (err) {
                console.error("Failed to toggle like:", err);
            }
        },

        async fetchLikedSongs() {
            try {
                const res = await axios.get("/api/like");
                const likedUUIDs: string[] = res.data.likes;

                if (!likedUUIDs.length) {
                    this.likedSongs = [];
                    return [];
                }

                // Fetch full _Song objects for all liked UUIDs
                const requests = likedUUIDs.map(uuid => axios.get(`/api/songs/${uuid}`));
                const responses = await Promise.all(requests);
                this.likedSongs = responses.map(r => r.data.data).filter(Boolean);

                return this.likedSongs;
            } catch (err) {
                console.error("Failed to fetch liked songs:", err);
                this.likedSongs = [];
                return [];
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
            }
        },
    },
});