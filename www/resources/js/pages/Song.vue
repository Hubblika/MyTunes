<script setup lang="ts">
import { MainLayout } from '@/layouts';
import type { Song } from '@/lib/types';

const {
    song
} = defineProps<{
    song?: Song
}>();

defineOptions({
    layout: MainLayout
});
</script>

<template>
    <pre>song = {{ song }}</pre>
</template>
