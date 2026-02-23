<script setup lang="ts">
import { Layout } from '@/layouts'
import { Header, Sidebar, Toolbar, MobileToolbar, MobileNavbar, PlayerShell, NowPlaying } from '@/components'
import { ref } from 'vue'

const nowPlayingRef = ref<InstanceType<typeof NowPlaying>>()
</script>

<template>
    <Layout>
        <PlayerShell>
            <div class="relative flex flex-col overflow-hidden h-screen">

                <!-- Desktop Header -->
                <div class="hidden lg:block">
                    <Header />
                </div>

                <!-- Main Content -->
                <main class="flex flex-1 w-full gap-4 px-4 py-4 overflow-auto custom-scrollbar">
                    <!-- Sidebar (desktop) -->
                    <div class="hidden lg:block">
                        <Sidebar />
                    </div>

                    <!-- Page content slot -->
                    <slot />
                </main>

                <!-- Desktop Toolbar -->
                <div class="hidden lg:block">
                    <Toolbar />
                </div>

                <!-- Mobile Toolbar and Navbar -->
                <div class="block lg:hidden">
                    <!-- Mobile Toolbar opens Now Playing overlay -->
                    <MobileToolbar class="fixed bottom-22 left-1/2 -translate-x-1/2 z-50"
                        @click="nowPlayingRef?.open()" />

                    <MobileNavbar class="fixed bottom-4 left-1/2 -translate-x-1/2 z-40" />
                </div>
            </div>

            <!-- Now Playing Overlay -->
            <NowPlaying ref="nowPlayingRef" />
        </PlayerShell>
    </Layout>
</template>