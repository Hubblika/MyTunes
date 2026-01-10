<script setup lang="ts">
import { MainContent, HomeContent } from '@/components';
import { MainLayout } from '@/layouts';

defineOptions({
    layout: MainLayout
});
</script>

<template>
    <MainContent>
        <HomeContent/>
    </MainContent>
</template>
