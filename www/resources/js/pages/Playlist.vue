<script setup lang="ts">
import { MainLayout } from '@/layouts';
import type { Playlist, Song } from '@/lib/types';

const {
    playlist,
    songs
} = defineProps<{
    playlist?: Playlist,
    songs?: Song[]
}>();

defineOptions({
    layout: MainLayout
});
</script>

<template>
    <pre>playlist = {{ playlist }}</pre>
    <pre>songs = {{ songs }}</pre>
</template>
