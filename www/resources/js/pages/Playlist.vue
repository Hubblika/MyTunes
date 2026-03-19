<script setup lang="ts">
import { MainContent, PlaylistContent, MobilePlaylistContent } from '@/components';
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
        <div class="hidden lg:block">
            <PlaylistContent :uuid="props.uuid" />
        </div>

        <div class="block lg:hidden">
            <MobilePlaylistContent :uuid="props.uuid" />
        </div>
    </MainContent>
</template>