<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { MainContent } from '@/components';
import { MainLayout } from '@/layouts';
import { HomeRow } from '@/components/common';

const { query } = defineProps<{ query: string }>();

let songs = ref([]);

async function search(q: string) {
    songs = await fetch(`/songs?q=${q}`, {
        headers: {
            accept: 'application/json; charset=utf-8'
        }
    }).then(r => r.json());
}

onMounted(async () => {
    await search(query);
});
</script>

<template>
    <MainLayout>
        <MainContent>
            <!-- TODO -->
        </MainContent>
    </MainLayout>
</template>
