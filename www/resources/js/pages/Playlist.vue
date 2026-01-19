<script setup lang="ts">
import { MainContent, PlaylistContent } from '@/components';
import { MainLayout } from '@/layouts';

defineOptions({
    layout: MainLayout
});

const props = defineProps<{
    uuid: string
}>();
</script>

<template>
    <MainContent>
        <PlaylistContent :uuid="props.uuid" />
    </MainContent>
</template>