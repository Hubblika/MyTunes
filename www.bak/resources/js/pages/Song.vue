<script setup lang="ts">
import { MainLayout } from '@/layouts';
import type { Song } from '@/lib/types';

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
