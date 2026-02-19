<script setup lang="ts">
import { Layout } from '@/layouts'
import { Header, Sidebar, Toolbar, MobileToolbar, MobileNavbar } from '@/components'
</script>

<template>
    <Layout>
        <div class="relative flex flex-col overflow-hidden h-screen">

            <div class="hidden lg:block">
                <Header />
            </div>

            <main class="flex flex-1 w-full gap-4 px-4 py-4 overflow-auto custom-scrollbar">
                <div class="hidden lg:block">
                    <Sidebar />
                </div>

                <slot />
            </main>

            <div class="hidden lg:block">
                <Toolbar />
            </div>

            <div class="block lg:hidden">
                <MobileToolbar class="fixed bottom-22 left-1/2 -translate-x-1/2 z-50" />

                <MobileNavbar class="fixed bottom-4 left-1/2 -translate-x-1/2 z-40" />
            </div>

        </div>
    </Layout>
</template>
