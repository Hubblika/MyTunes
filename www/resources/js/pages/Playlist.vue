<script setup lang="ts">
import { MainContent, PlaylistContent } from '@/components';
import { MainLayout } from '@/layouts';

defineOptions({
    layout: MainLayout
});
</script>

<template>
    <MainContent>
        <PlaylistContent/>
    </MainContent>
</template>