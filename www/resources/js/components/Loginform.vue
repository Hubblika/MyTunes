<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount, computed } from 'vue'
import { Form, router } from '@inertiajs/vue3'
import { Input, PrimaryButton, TextLink } from './common'
import { store as loginStore } from '@/routes/login'
import { store as registerStore } from '@/routes/register'

const { register } = defineProps<{
    register?: boolean
}>()

const password = ref('')
const password2 = ref('')
const remember = ref(false)
const logoSrc = ref('')

const currentStore = computed(() => register ? registerStore : loginStore)

function updateLogo() {
    const isDark = document.documentElement.classList.contains('dark')
    logoSrc.value = isDark
        ? '/uploads/logos/logo_dark.svg'
        : '/uploads/logos/logo_light.svg'
}

let observer: MutationObserver | null = null

onMounted(() => {
    updateLogo()
    observer = new MutationObserver(updateLogo)
    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['class'],
    })
})

onBeforeUnmount(() => {
    observer?.disconnect()
})
</script>

<template>
    <main class="relative w-full text-neutral-900 dark:text-white">
        <div class="relative z-10 flex min-h-screen items-center justify-center p-6">
            <div class="flex w-full max-w-sm flex-col items-center gap-6 text-center">
                <img :src="logoSrc" alt="Logo" class="h-32 w-auto select-none opacity-90" />

                <div
                    class="w-full rounded-2xl border border-black/10 dark:border-white/10 bg-white/80 dark:bg-white/5 backdrop-blur-xl px-8 py-10 shadow-xl">
                    <h1 class="mb-8 text-2xl font-semibold tracking-tight">
                        <span class="bg-linear-to-r from-fuchsia-500 to-cyan-500 bg-clip-text text-transparent">
                            {{ register ? $t('register.title') : $t('login.title') }}
                        </span>
                    </h1>

                    <Form v-bind="currentStore.form()" :reset-on-success="['password']" v-slot="{ processing, errors }"
                        novalidate class="flex flex-col gap-4 text-left">

                        <div class="flex flex-col gap-1">
                            <Input id="email" type="email" required autofocus
                                :placeholder="register ? $t('register.emailPlaceholder') : $t('login.emailPlaceholder')">
                                {{ register ? $t('register.emailLabel') : $t('login.emailLabel') }}
                            </Input>
                            <span v-if="errors.email" class="ml-1 text-[11px] font-bold uppercase text-red-500">
                                {{ $t(errors.email) }}
                            </span>
                        </div>

                        <div class="flex flex-col gap-1">
                            <Input v-model="password" id="password" type="password" required
                                :placeholder="register ? $t('register.passwordPlaceholder') : $t('login.passwordPlaceholder')">
                                {{ register ? $t('register.passwordLabel') : $t('login.passwordLabel') }}
                            </Input>

                            <div v-if="!register" class="mt-2 flex items-center justify-between px-1">
                                <div class="flex items-center gap-2">
                                    <div class="relative flex items-center">
                                        <input v-model="remember" id="remember" type="checkbox" name="remember"
                                            class="peer h-5 w-5 cursor-pointer appearance-none rounded-lg border border-black/10 dark:border-white/20 bg-white/20 dark:bg-white/5 backdrop-blur-md transition-all checked:border-cyan-500/50 checked:bg-cyan-500/20 hover:bg-white/40 dark:hover:bg-white/10 shadow-inner" />

                                        <svg class="pointer-events-none absolute h-5 w-5 p-1 text-cyan-500 opacity-0 transition-opacity peer-checked:opacity-100"
                                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="4" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    </div>
                                    <label for="remember"
                                        class="cursor-pointer text-xs font-medium text-neutral-600 dark:text-white/60 select-none">
                                        {{ $t('login.rememberMe') }}
                                    </label>
                                </div>

                                <button type="button" @click="router.get('/forgot-password')"
                                    class="text-xs font-medium text-cyan-600 dark:text-cyan-400 hover:text-fuchsia-500 transition-colors cursor-pointer">
                                    {{ $t('login.forgotPassword') }}
                                </button>
                            </div>

                            <span v-if="errors.password" class="ml-1 text-[11px] font-bold uppercase text-red-500">
                                {{ $t(errors.password) }}
                            </span>
                        </div>

                        <div v-if="register" class="flex flex-col gap-1">
                            <Input v-model="password2" id="password_confirmation" type="password" required
                                :placeholder="$t('register.confirmPasswordPlaceholder')">
                                {{ $t('register.confirmPasswordLabel') }}
                            </Input>
                        </div>

                        <PrimaryButton type="submit" :disabled="processing" class="mt-4">
                            <span class="relative z-10">
                                {{ processing ? (register ? $t('register.statusMessage') : $t('login.statusMessage')) :
                                    (register ? $t('register.mainButton') : $t('login.mainButton')) }}
                            </span>
                        </PrimaryButton>
                    </Form>

                    <span class="mt-8 block text-center text-sm text-neutral-600 dark:text-white/60">
                        {{ register ? $t('register.loginText') : $t('login.signUpText') }}
                        <TextLink @click="() => router.get(register ? '/login' : '/register')"
                            class="ml-1 font-medium text-cyan-600 dark:text-cyan-400 hover:text-fuchsia-500 transition-colors">
                            {{ register ? $t('register.loginButton') : $t('login.signUpButton') }}
                        </TextLink>
                    </span>
                </div>
            </div>
        </div>
    </main>
</template>