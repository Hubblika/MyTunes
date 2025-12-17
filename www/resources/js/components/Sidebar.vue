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
    coverUrl: '/uploads/thumbnails/playlist/defaultThumbnail.png'
  },
  {
    id: 2,
    title: 'Playlist 1',
    subtitle: '42 songs',
    coverUrl: '/uploads/thumbnails/playlist/defaultThumbnail.png'
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

function deletePlaylist(playlistID: number) {
    playlists.value = playlists.value.filter(item => item.id !== playlistID);
    //TODO: delete playlist from the database
}
</script>

<template>
    <aside class="bg-gray-500/6 dark:text-white w-96 flex flex-col h-full px-4 rounded-lg">
        <div class="flex items-center justify-between h-16 shrink-0">
            <h1 class="text-lg font-bold pl-2">My music</h1>
            <Button @click="addPlaylist" class="group relative">
                <Icon
                    name="circle-plus"
                    class="w-5 h-5 transition-all duration-200
                        group-hover:text-black/60 dark:group-hover:text-white/80"/>
            </Button>
        </div>
        <Searchbar class="w-full shrink-0"></Searchbar>
        <div class="space-y-1 pt-3 max-h-full flex-1 overflow-y-auto">
            <PlaylistCard
                 v-for="playlist in playlists"
                :id="playlist.id"
                :title="playlist.title"
                :subtitle="playlist.subtitle"
                :cover-url="playlist.coverUrl"
                @deletePlaylist="deletePlaylist"/>
        </div>
    </aside>
</template>
