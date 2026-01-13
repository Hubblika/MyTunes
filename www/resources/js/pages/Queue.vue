<script setup lang="ts">
import { LyricsContent, MainContent,  } from '@/components';
import { MainLayout } from '@/layouts';

defineOptions({
    layout: MainLayout
});
</script>

<template>
    <MainContent>
        <LyricsContent/>
    </MainContent>
</template>