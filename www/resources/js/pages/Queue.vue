<script setup lang="ts">
import { QueueContent, MainContent,  } from '@/components';
import { MainLayout } from '@/layouts';

defineOptions({
    layout: MainLayout
});
</script>

<template>
    <MainContent>
        <QueueContent/>
    </MainContent>
</template>