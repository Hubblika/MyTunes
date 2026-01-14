<script setup lang="ts">
import { Layout } from '@/layouts';

const { status, message } = defineProps<{ status: number, message?: string }>();

defineOptions({
    layout: Layout,
});
</script>

<template>
    <div class="size-full flex flex-col justify-center items-center gap-4">
        <h1 class="font-semibold text-6xl">{{ status }}</h1>
        <span v-if="!!message" class="text-xl">{{ message }}</span>
    </div>
</template>
