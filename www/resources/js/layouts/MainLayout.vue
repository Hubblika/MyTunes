<script setup lang="ts">
import { Header } from '@/components';
</script>

<template>
    <Header></Header>
    <main class="w-full min-h-screen px-8 pt-20 flex flex-col gap-2">
        <slot></slot>
    </main>
    <!-- <Footer></Footer> -->
</template>
