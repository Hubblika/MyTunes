<script setup lang="ts">
import { Searchbar, PlaylistCard, Button, Icon } from './common'
import { ref } from 'vue'

type Playlist = {
  id: number
  title: string
  subtitle?: string
  coverUrl: string
  //TODO: add property to define the music in the playlist
}

const playlists = ref<Playlist[]>([
  {
    id: 1,
    title: 'Liked Songs',
    subtitle: '128 songs',
    coverUrl: '/covers/liked.png'
  },
  {
    id: 2,
    title: 'Playlist 1',
    subtitle: '42 songs',
    coverUrl: '/covers/chill.jpg'
  }
]);

function fetchplaylists() {
    //TODO: fetch playlists from the database
}

function addPlaylist() {
  playlists.value.push({
    id: Date.now(),
    title: 'New Playlist',
    subtitle: '0 songs',
    coverUrl: '/covers/default-playlist.png'
  })

  //TODO: save added playlist to database
}
</script>

<template>
    <aside class="bg-gray-500/6 dark:text-white w-96 min-h-full px-4 rounded-lg">
        <div class="flex items-center justify-between h-16">
            <h1 class="text-lg font-bold pl-2">My music</h1>
            <Button @click="addPlaylist" class="group relative">
                <Icon
                    name="circle-plus"
                    class="w-5 h-5 transition-all duration-200
                        group-hover:text-black/60 dark:group-hover:text-white/80"/>
            </Button>
        </div>
        <Searchbar class="w-full"></Searchbar>
        <div class="space-y-1 pt-3">
            <PlaylistCard
                 v-for="playlist in playlists"
                :key="playlist.id"
                :title="playlist.title"
                :subtitle="playlist.subtitle"
                :cover-url="playlist.coverUrl"/>
        </div>
    </aside>
</template>
