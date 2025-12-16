<script setup lang="ts">
import { Searchbar, PlaylistCard } from './common';
</script>

<template>
    <aside class="bg-gray-500/6 dark:bg-black dark:text-white w-96 min-h-full px-4 rounded-lg">
        <div class="flex items-center h-16">
            <h1 class="text-lg font-bold pl-2">My music</h1>
        </div>
        <Searchbar class="w-full"></Searchbar>
        <div class="space-y-1 pt-3">
            <PlaylistCard
                title="Liked Songs"
                subtitle="128 songs"/>
            <PlaylistCard
                title="Playlist 1"
                subtitle="42 songs"
                cover-url="/covers/chill.jpg"/>
            <PlaylistCard
                title="Playlist 2"
                subtitle="30 songs"/>
        </div>
    </aside>
</template>
