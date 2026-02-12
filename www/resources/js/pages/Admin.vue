<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { Layout } from '@/layouts'
import { Button, Icon } from '@/components/common'
import { SongUploadForm, EditPermission } from '@/components'

const showUploadForm = ref(false)
const showEditPermission = ref(false)

const handleClose = () => {
    if (showUploadForm.value) {
        showUploadForm.value = false
    } 
    else if (showEditPermission.value) {
        showEditPermission.value = false
    } else {
        router.visit('/')
    }
}
</script>

<template>
    <Layout>
        <div class="relative min-h-[80vh] flex items-center justify-center px-6">

            <button @click="handleClose" class="absolute top-6 right-6
               p-2 rounded-full
               bg-white/30 dark:bg-white/10
               backdrop-blur-md
               border border-black/10 dark:border-white/10
               hover:scale-110 transition">
                <Icon name="x" class="size-6 text-black dark:text-white" />
            </button>

            <div class="flex flex-col items-center text-center space-y-12">

                <h1 v-if="!showUploadForm && !showEditPermission" class="text-4xl font-bold tracking-tight">
                    <span class="bg-linear-to-r from-fuchsia-500 to-cyan-500
                   bg-clip-text text-transparent">
                        {{ $t('admin.title') }}
                    </span>
                </h1>

                <div v-if="!showUploadForm && !showEditPermission" class="flex flex-col sm:flex-row gap-8">
                    <Button @click="showUploadForm = true" class="group flex flex-col items-center justify-center
                   w-56 h-44
                   rounded-3xl
                   border border-black/10 dark:border-white/10
                   bg-white/30 dark:bg-white/5
                   backdrop-blur-xl
                   shadow-xl dark:shadow-2xl
                   transition-all duration-300
                   hover:scale-105 hover:shadow-2xl">
                        <Icon name="file-upload" class="size-14 mb-4
                     text-black dark:text-white
                     transition-transform duration-300
                     group-hover:scale-110" />
                        <span class="text-lg font-medium text-black dark:text-white">
                            {{ $t('admin.songUpload') }}
                        </span>
                    </Button>

                    <Button @click="showEditPermission = true" class="group flex flex-col items-center justify-center
                   w-56 h-44
                   rounded-3xl
                   border border-black/10 dark:border-white/10
                   bg-white/30 dark:bg-white/5
                   backdrop-blur-xl
                   shadow-xl dark:shadow-2xl
                   transition-all duration-300
                   hover:scale-105 hover:shadow-2xl">
                        <Icon name="user-edit" class="size-14 mb-4
                     text-black dark:text-white
                     transition-transform duration-300
                     group-hover:scale-110" />
                        <span class="text-lg font-medium text-black dark:text-white">
                            {{ $t('admin.editPermissions') }}
                        </span>
                    </Button>
                </div>

                <SongUploadForm v-if="showUploadForm" class="w-full max-w-2xl" />

                <EditPermission v-if="showEditPermission" class="w-full max-w-2xl" />
            </div>
        </div>
    </Layout>
</template>
