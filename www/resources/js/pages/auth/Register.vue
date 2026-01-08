<script setup lang="ts">
import { Loginform } from '@/components';
import { Layout } from '@/layouts';

defineOptions({
    layout: Layout
});
</script>

<template>
    <main class="text-black dark:text-white bg-white dark:bg-black w-full">
        <Loginform register></Loginform>
    </main>
</template>
