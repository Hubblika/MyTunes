<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useForm } from '@inertiajs/vue3'
import { PrimaryButton, ToggleSwitch } from '@/components/common'

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
        await axios.put('/users/admin-status', { users: form.users })
        alert('Admin status updated successfully!')
    } catch (err) {
        console.error(err)
        alert('Failed to update admin status.')
    } finally {
        form.processing = false
    }
}

onMounted(async () => {
    await fetchUsers()
})
</script>

<template>
    <main class="relative w-full text-neutral-900 dark:text-white">
        <div class="relative z-10 flex max-h-screen items-center justify-center p-6">
            <div class="flex min-w-md flex-col gap-6">
                <div class="w-full rounded-2xl
                border border-black/10 dark:border-white/10
                bg-white/80 dark:bg-white/5
                backdrop-blur-xl
                px-10 py-10
                shadow-xl dark:shadow-2xl
                shadow-black/10 dark:shadow-black/40">

                    <h1 class="mb-8 text-2xl font-semibold tracking-tight text-center">
                        <span class="bg-linear-to-r from-fuchsia-500 to-cyan-500
                    bg-clip-text text-transparent">
                            User Administration
                        </span>
                    </h1>

                    <div v-if="loading" class="text-center py-4">Loading users...</div>
                    <div v-if="error" class="text-red-500 text-center">{{ error }}</div>

                    <form @submit.prevent="submit" class="flex flex-col gap-5 text-left" v-if="!loading && !error">
                        <div v-for="user in form.users" :key="user.id" class="flex items-center justify-between gap-4
                        rounded-xl border border-black/10 dark:border-white/10
                        bg-white dark:bg-black/30 px-4 py-3 hover:bg-black/5 dark:hover:bg-white/10
                        transition">

                            <span>{{ user.email }}</span>

                            <ToggleSwitch v-model="user.is_admin"/>
                        </div>

                        <PrimaryButton type="submit" :disabled="form.processing"
                            class="mt-6 flex items-center justify-center gap-2">
                            <svg v-if="form.processing" class="animate-spin h-5 w-5 text-white"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span>{{ form.processing ? 'Saving...' : 'Save Changes' }}</span>
                        </PrimaryButton>
                    </form>

                </div>
            </div>
        </div>
    </main>
</template>
