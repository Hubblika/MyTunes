<script setup lang="ts">
import { MainLayout } from '@/layouts';
import type { Song } from '@/types';

const {
    song
} = defineProps<{
    song?: Song
}>();
</script>

<template>
    <MainLayout>
        <pre>song = {{ song }}</pre>
    </MainLayout>
</template>
