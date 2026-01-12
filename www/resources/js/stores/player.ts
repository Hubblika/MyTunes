// stores/player.ts
import { defineStore } from 'pinia'
import type { _Song } from '@/types'

export const usePlayerStore = defineStore('player', {
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
    audioSrc: (state) => state.queue[state.currentIndex]?.url ?? '',
  },

  actions: {
    playSong(song: _Song) {
      const index = this.queue.findIndex(s => s.uuid === song.uuid)

      if (index === -1) {
        this.queue = [song]
        this.currentIndex = 0
      } else {
        this.currentIndex = index
      }

      this.isPlaying = true
    },

    togglePlay() {
      this.isPlaying = !this.isPlaying
    },

    addToQueue(song: _Song) {
      this.queue.push(song)
    },

    next() {
      if (this.currentIndex < this.queue.length - 1) {
        this.currentIndex++
      } else if (this.loop) {
        this.currentIndex = 0
      }
    },

    previous() {
      if (this.currentIndex > 0) {
        this.currentIndex--
      }
    }
  }
})