<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useForm } from '@inertiajs/vue3'
import { PrimaryButton, ToggleSwitch } from '@/components/common'
import { useI18n } from 'vue-i18n'

interface User {
    id: number
    email: string
    is_admin: boolean
}

const form = useForm<{ users: User[] }>({
    users: [],
})

const loading = ref(false)
const error = ref('')
const { t } = useI18n()

async function fetchUsers() {
    loading.value = true
    error.value = ''
    try {
        const response = await axios.get('/users')
        form.users = response.data.data ?? response.data
    } catch (err) {
        error.value = 'Failed to load users.'
        console.error(err)
    } finally {
        loading.value = false
    }
}


async function submit() {
    form.processing = true
    try {
        await axios.put('/admin', { users: form.users })
        alert(t('admin.permissionsUpdateSuccess'))
    } catch (err) {
        console.error(err)
        alert(t('admin.permissionsUpdateError'))
    } finally {
        form.processing = false
    }
}

onMounted(async () => {
    await fetchUsers()
})
</script>

<template>
    <main class="relative w-full min-h-screen text-neutral-900 dark:text-white">
        <div class="relative z-10 flex min-h-screen items-center justify-center p-4 sm:p-6">

            <div class="w-full max-w-md">
                <div class="rounded-2xl
                            border border-black/10 dark:border-white/10
                            bg-white/80 dark:bg-white/5
                            backdrop-blur-xl
                            px-5 py-6 sm:px-8 sm:py-8 md:px-10 md:py-10
                            shadow-xl dark:shadow-2xl
                            shadow-black/10 dark:shadow-black/40">

                    <h1 class="mb-6 sm:mb-8 text-xl sm:text-2xl font-semibold tracking-tight text-center">
                        <span class="bg-linear-to-r from-fuchsia-500 to-cyan-500 bg-clip-text text-transparent">
                            {{ $t('admin.permissionsTitle') }}
                        </span>
                    </h1>

                    <div v-if="loading" class="text-center py-4">
                        {{ $t('admin.loadingMessage') }}
                    </div>

                    <div v-if="error" class="text-red-500 text-center">
                        {{ error }}
                    </div>

                    <form v-if="!loading && !error" @submit.prevent="submit"
                        class="flex flex-col gap-4 sm:gap-5 text-left">


                        <div class="flex flex-col gap-3
                                    max-h-[45vh] sm:max-h-[50vh] md:max-h-[55vh]
                                    overflow-y-auto custom-scrollbar">
                            <div v-for="user in form.users" :key="user.id" class="flex items-center justify-between gap-3 sm:gap-4
                            rounded-xl border border-black/10 dark:border-white/10
                            bg-white dark:bg-black/30
                            px-3 py-2 sm:px-4 sm:py-3
                            hover:bg-black/5 dark:hover:bg-white/10 transition">
                                <span class="text-sm sm:text-base break-all">
                                    {{ user.email }}
                                </span>

                                <ToggleSwitch v-model="user.is_admin" :disabled="user.email === 'admin@example.com'"
                                    class="shrink-0" />
                            </div>
                        </div>

                        <div class="pt-3 sm:pt-4 border-t border-black/10 dark:border-white/10">
                            <PrimaryButton type="submit" :disabled="form.processing"
                                class="mt-2 flex items-center justify-center gap-2 w-full">
                                <svg v-if="form.processing" class="animate-spin h-5 w-5 text-white"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" />
                                </svg>

                                <span>
                                    {{ form.processing ? $t('admin.processingMessage') : $t('admin.saveButton') }}
                                </span>
                            </PrimaryButton>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </main>
</template>
