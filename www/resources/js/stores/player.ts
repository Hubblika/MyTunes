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
            console.log(current);
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
                    await axios.post(`/likes/${track.uuid}`);
                    this.likedSongs.push(track);
                } else {
                    await axios.delete(`/likes/${track.uuid}`);
                    this.likedSongs.splice(likedIndex, 1);
                }
            } catch (err) {
                console.error("Failed to toggle like:", err);
            }
        },

        async fetchLikedSongs() {
            try {
                const res = await axios.get("/likes");
                const likedData: { uuid: string; added_at: string }[] = res.data.likes || [];

                if (!likedData.length) {
                    this.likedSongs = [];
                    return [];
                }

                const requests = likedData.map((item) =>
                    axios.get(`/songs/${item.uuid}`)
                );

                const responses = await Promise.all(requests);

                this.likedSongs = responses
                    .map((r, i) => {
                        const song = r.data;
                        if (!song) return null;

                        return {
                            ...song,
                            added_at: likedData[i].added_at,
                            url: song.url || `http://localhost:8000/stream/${song.uuid}`
                        };
                    })
                    .filter(Boolean) as _Song[];

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

        async fetchPlaylist(uuid: string) {
            try {
                const { data } = await axios.get(`/playlists/${uuid}`);
                const pl: _Playlist = data.data ?? data;
                if (!pl.songs) pl.songs = [];
                this.setPlaylist(pl);
                return pl;
            } catch (err) {
                console.error("Failed to fetch playlist", err);
                return null;
            }
        },

        async renamePlaylistAPI(uuid: string, name: string) {
            try {
                const { data } = await axios.put(`/playlists/${uuid}`, {
                    name,
                });
                this.renamePlaylist(uuid, data.data.name);
                return data.data;
            } catch (err) {
                console.error("Failed to rename playlist", err);
                return null;
            }
        },

        async deletePlaylistAPI(uuid: string) {
            try {
                await axios.delete(`/playlists/${uuid}`);
                this.deletePlaylist(uuid);
                return true;
            } catch (err) {
                console.error("Failed to delete playlist", err);
                return false;
            }
        },

        async fetchPlaylists(username: string) {
            try {
                const { data } = await axios.get(
                    `/playlists?username=${username}`,
                    {
                        headers: { Accept: "application/json" },
                    },
                );
                const fetchedPlaylists: _Playlist[] = data.data;
                fetchedPlaylists.forEach((pl) => this.setPlaylist(pl));
                return fetchedPlaylists;
            } catch (err) {
                console.error("Failed to fetch playlists", err);
                return [];
            }
        },

        async addPlaylist(name: string = "New Playlist") {
            try {
                const { data } = await axios.post("/playlists", { name });
                const newPlaylist = data.data as _Playlist;
                this.setPlaylist(newPlaylist);
                return newPlaylist;
            } catch (err) {
                console.error("Failed to add playlist", err);
                return null;
            }
        },

        async updatePlaylist(
            uuid: string,
            payload: { name?: string; description?: string; cover?: File },
        ) {
            try {
                console.log(payload)
                const formData = new FormData();

                formData.append("_method", "PUT");

                if (payload.name) {
                    formData.append("name", payload.name);
                }

                if (payload.description !== undefined) {
                    formData.append("description", payload.description);
                }

                if (payload.cover) {
                    formData.append("cover", payload.cover);
                }

                // We use axios.post here!
                const { data } = await axios.post(
                    `/playlists/${uuid}`,
                    formData,
                    {
                        headers: {
                            "Content-Type": "multipart/form-data"
                        },
                    },
                );

                const pl = this.playlists.get(uuid);
                if (pl && data.data) {
                    const updated: _Playlist = {
                        ...pl,
                        name: data.data.name ?? pl.name,
                        description: data.data.description !== undefined
                            ? data.data.description
                            : pl.description,
                        cover_url: data.data.cover_url ?? pl.cover_url,
                        updated_at: data.data.updated_at ?? new Date().toISOString(),
                    };

                    this.playlists.set(uuid, updated);
                }

                return data.data;
            } catch (err) {
                console.error("Failed to update playlist:", err);
                return null;
            }
        },

        async addSongToPlaylist(playlistUuid: string, songUuid: string) {
            try {
                await axios.post(`/playlists/${playlistUuid}/songs`, {
                    song_id: songUuid,
                });

                const pl = this.playlists.get(playlistUuid);
                if (!pl) return true;

                if (pl.songs) {
                    const song =
                        this.queue.find((s) => s.uuid === songUuid) ||
                        this.likedSongs.find((s) => s.uuid === songUuid);

                    if (song) pl.songs.push(song);
                }

                if (typeof pl.songs_count === "number") {
                    pl.songs_count++;
                }

                return true;
            } catch (err) {
                console.error(
                    `Failed to add song ${songUuid} to playlist ${playlistUuid}`,
                    err,
                );
                return false;
            }
        },

        async fetchPlaylistSongs(uuid: string) {
            try {
                const res = await axios.get(`/playlists/${uuid}/songs`);

                const songs = res.data.map((song: any) => ({
                    ...song,
                    added_at: song.pivot?.created_at,
                    url: `http://localhost:8000/stream/${song.uuid}`
                })) as _Song[];

                const pl = this.playlists.get(uuid);
                if (!pl) return;

                pl.songs = songs;
                pl.songs_count = songs.length;
            } catch (err) {
                console.error("Failed to fetch playlist songs", err);
            }
        },

        async containsSong(playlist: _Playlist, song: _Song) {
            try {
                const res = await axios.get(
                    `/playlists/${playlist.uuid}/songs`,
                );
                const songIds: string[] = res.data.map((s: any) => s.song_id);

                if (songIds.includes(song.uuid)) {
                    return true;
                } else {
                    return false;
                }
            } catch (err) {
                console.error("Failed to fetch playlist songs", err);
                return false;
            }
        },

        async deleteSong(playlist: _Playlist, song: _Song) {
            try {
                await axios.delete(
                    `/playlists/${playlist.uuid}/songs/${song.uuid}`,
                );
                return true;
            } catch (err) {
                console.error("Failed to delete song from playlist", err);
                return false;
            }
        },
    },
});
