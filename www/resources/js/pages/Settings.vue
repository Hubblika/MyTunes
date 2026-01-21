<script setup lang="ts">
import { MainContent, SettingsMenu } from '@/components';
import { MainLayout } from '@/layouts';

defineOptions({
    layout: MainLayout
});
</script>

<template>
    <MainContent>
        <SettingsMenu></SettingsMenu>
    </MainContent>
</template>