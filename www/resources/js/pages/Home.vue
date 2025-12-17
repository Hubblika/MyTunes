<script setup lang="ts">
import { MainContent } from '@/components';
import { MainLayout } from '@/layouts';
import { usePage } from '@inertiajs/vue3';

const page = usePage();

console.log(page.props.auth.user);
</script>

<template>
    <MainLayout>
        <MainContent></MainContent>
    </MainLayout>
</template>
