<script setup lang="ts">
import { Layout } from '@/layouts'
import { Header, Sidebar, Toolbar } from '@/components'
</script>

<template>
  <Layout>
    <!-- COLUMN STACK -->
    <div class="relative z-10 flex min-h-screen flex-col">

      <Header />

      <!-- MAIN CONTENT AREA -->
      <main class="flex flex-1 w-full gap-4 px-4 py-4">
        <Sidebar />
        <slot />
      </main>

      <Toolbar />

    </div>
  </Layout>
</template>
