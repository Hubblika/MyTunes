<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { usePlayerStore } from '@/stores/player';
import { MobilePlaylistSong } from './common';
import { _Song } from '@/types';

const props = defineProps<{ playlistUuid: string }>();
const player = usePlayerStore();

const songs = computed<_Song[]>(() => {
  if (props.playlistUuid === '00000000-0000-0000-0000-000000000000') {
    return player.likedSongList;
  }
  const playlist = player.playlists.get(props.playlistUuid);
  return playlist?.songs ?? [];
});

onMounted(async () => {
  if (props.playlistUuid === '00000000-0000-0000-0000-000000000000') {
    await player.fetchLikedSongs();
  } else {
    await player.fetchPlaylist(props.playlistUuid);
    await player.fetchPlaylistSongs(props.playlistUuid);
  }
});
</script>

<template>
  <div class="flex flex-col gap-2 overflow-y-auto custom-scrollbar p-4">
    <MobilePlaylistSong
      v-for="(song, index) in songs"
      :key="song.uuid"
      :index="index + 1"
      :song="song"
      :playlist-uuid="props.playlistUuid"
    />
  </div>
</template>
