import { defineStore } from "pinia";
import type { _Song } from "@/types";
import type { _Playlist } from "@/types";
import axios from "axios";

export const usePlayerStore = defineStore("player", {
    state: () => ({
        queue: [] as _Song[],
        originalQueue: [] as _Song[],
        currentIndex: 0,
        isPlaying: false,
        shuffle: false,
        loop: false,
        loopTrack: false,
        likedSongs: [] as _Song[],
        time: 0,
        duration: 0,
        volume: 10,
        currentPlaylist: null as string | null,
        playlists: new Map<string, _Playlist>(),
        tempQueue: [] as _Song[],
        tempQueueIndex: 0,
        previousQueue: [] as _Song[],
        previousIndex: 0,
        previousPlaylist: null as string | null,
    }),

    getters: {
        currentTrack: (state) => {
            if (state.tempQueue.length > 0) {
                return state.tempQueue[state.tempQueueIndex];
            }
            return state.queue[state.currentIndex];
        },
        audioSrc: (state) => {
            if (state.tempQueue.length > 0)
                return state.tempQueue[state.tempQueueIndex]?.url ?? "";
            return state.queue[state.currentIndex]?.url ?? "";
        },
        uuid: (state) => state.queue[state.currentIndex]?.uuid,
        isLiked: (state) => (song?: _Song) =>
            song ? state.likedSongs.some((s) => s.uuid === song.uuid) : false,
        likedCount: (state) => state.likedSongs.length,
        likedSongList: (state) => state.likedSongs,
    },

    actions: {
        async playSong(song: _Song) {
            const current = this.currentTrack;
            if (!current || current.uuid !== song.uuid) {
                if (!this.queue.length) {
                    this.queue = [song];
                } else {
                    const idx = this.queue.findIndex(
                        (s) => s.uuid === song.uuid,
                    );
                    if (idx !== -1) this.queue.splice(idx, 1);
                    this.queue.unshift(song);
                }
                this.currentIndex = 0;
                this.isPlaying = true;
            }
        },

        togglePlay() {
            this.isPlaying = !this.isPlaying;
        },

        addPlaylistToQueue(playlist: _Playlist) {
            if (!playlist.songs || !playlist.songs.length) return;
            this.playlists.set(playlist.uuid, playlist);
            this.queue = [...playlist.songs];
            this.originalQueue = [...playlist.songs];
            this.currentIndex = 0;
            this.isPlaying = true;
            this.currentPlaylist = playlist.uuid;
        },

        addToQueue(songs: _Song | _Song[]) {
            const songArray = Array.isArray(songs) ? songs : [songs];
            if (!songArray.length) return;

            const insertIndex = this.currentIndex + 1;

            const filteredSongs = songArray.filter(
                (s) => !this.queue.some((q) => q.uuid === s.uuid),
            );

            this.queue.splice(insertIndex, 0, ...filteredSongs);

            if (!this.currentPlaylist) {
                this.currentPlaylist = null;
            }

            this.isPlaying = true;
        },

        next() {
            if (this.loopTrack) {
                this.time = 0;
                return;
            }

            if (this.tempQueue.length > 0) {
                if (this.tempQueueIndex < this.tempQueue.length - 1) {
                    this.tempQueueIndex++;
                } else {
                    this.queue = [...this.previousQueue];
                    this.currentIndex = this.previousIndex;
                    this.currentPlaylist = this.previousPlaylist;
                    this.tempQueue = [];
                    this.tempQueueIndex = 0;
                    if (this.isPlaying && this.currentTrack) {
                    } else {
                        this.isPlaying = false;
                    }
                }
                this.time = 0;
                return;
            }

            if (this.currentIndex < this.queue.length - 1) {
                this.currentIndex++;
            } else if (this.loop) {
                this.currentIndex = 0;
            } else {
                this.isPlaying = false;
            }
        },

        previous() {
            if (this.tempQueue.length > 0) {
                if (this.tempQueueIndex > 0) this.tempQueueIndex--;
                this.time = 0;
                return;
            }

            if (this.currentIndex > 0) this.currentIndex--;
            else if (this.loop) this.currentIndex = this.queue.length - 1;
            this.time = 0;
        },

        emptyQueue() {
            this.queue = [];
            this.originalQueue = [];
            this.currentIndex = 0;
            this.isPlaying = false;
        },

        shuffleQueue() {
            if (this.queue.length <= 1) return;

            if (!this.shuffle) {
                this.originalQueue = [...this.queue];
            }
            this.shuffle = true;

            const currentTrack = this.queue[this.currentIndex];

            for (let i = this.queue.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [this.queue[i], this.queue[j]] = [this.queue[j], this.queue[i]];
            }

            this.currentIndex = this.queue.findIndex(
                (song) => song.uuid === currentTrack.uuid,
            );
        },

        sortQueue() {
            if (!this.shuffle) return;
            const currentTrack = this.queue[this.currentIndex];
            this.queue = [...this.originalQueue];
            this.currentIndex = this.queue.findIndex(
                (song) => song.uuid === currentTrack.uuid,
            );
            this.shuffle = false;
        },

        async toggleLike(song?: _Song) {
            const track = song ?? this.currentTrack;
            if (!track) return;

            const likedIndex = this.likedSongs.findIndex(
                (s) => s.uuid === track.uuid,
            );

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
                const likedUUIDs: string[] = res.data.likes || [];
                if (!likedUUIDs.length) {
                    this.likedSongs = [];
                    return [];
                }

                const requests = likedUUIDs.map((uuid) =>
                    axios.get(`/api/songs/${uuid}`),
                );
                const responses = await Promise.all(requests);
                this.likedSongs = responses
                    .map((r) => r.data.data)
                    .filter(Boolean);

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
