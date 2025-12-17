<script setup lang="ts">
import { Root } from '@/layouts';
import { Header, Sidebar, Toolbar } from '@/components';
</script>

<template>
    <Root>
        <Header></Header>
        <main class="flex-1 text-black dark:text-white bg-white dark:bg-black w-full flex flex-row gap-2 px-2">
            <Sidebar></Sidebar>
            <slot></slot>
        </main>
        <Toolbar></Toolbar>
    </Root>
</template>
