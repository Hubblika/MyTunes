<script setup lang="ts">
import { Loginform } from '@/components';
import { Layout } from '@/layouts';

defineOptions({
    layout: Layout
});
</script>

<template>
    <main class="w-full">
        <Loginform register></Loginform>
    </main>
</template>
