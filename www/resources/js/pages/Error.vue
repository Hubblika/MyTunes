<script setup lang="ts">
import { MainLayout } from '@/layouts';

const { status, message } = defineProps<{ status: number, message?: string }>();
</script>

<template>
    <MainLayout>
        <div class="size-full flex flex-col justify-center items-center gap-4">
            <h1 class="font-semibold text-6xl">{{ status }}</h1>
            <span v-if="!!message" class="text-xl">{{ message }}</span>
        </div>
    </MainLayout>
</template>
