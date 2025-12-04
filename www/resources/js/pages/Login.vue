<script setup lang="ts">
import { MainLayout } from '@/layouts';
import type { Playlist, Song } from '@/types';

const {
    playlist,
    songs
} = defineProps<{
    playlist?: Playlist,
    songs?: Song[]
}>();
</script>

<template>
    <MainLayout>
        <pre>playlist = {{ playlist }}</pre>
        <pre>songs = {{ songs }}</pre>
    </MainLayout>
</template>
