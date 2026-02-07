<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { Form, router } from '@inertiajs/vue3'
import { Input, PrimaryButton, TextLink } from './common'
import { store as loginStore } from '@/routes/login'
import { store as registerStore } from '@/routes/register'

const { register } = defineProps<{
    register?: boolean
}>()

const password = ref('')
const password2 = ref('')
const logoSrc = ref('')

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
                <img :src="logoSrc" alt="Logo" class="h-32 w-auto select-none
                 opacity-90 dark:opacity-100
                 drop-shadow-sm
                 dark:drop-shadow-[0_0_12px_rgba(255,255,255,0.15)]" />

                <div class="w-full rounded-2xl
                 border border-black/10 dark:border-white/10
                 bg-white/80 dark:bg-white/5
                 backdrop-blur-xl
                 px-8 py-10
                 shadow-xl dark:shadow-2xl
                 shadow-black/10 dark:shadow-black/40">
                    <h1 class="mb-8 text-2xl font-semibold tracking-tight">
                        <span class="bg-linear-to-r from-fuchsia-500 to-cyan-500
                     bg-clip-text text-transparent">
                            {{ register ? $t('register.title') : $t('login.title') }}
                        </span>
                    </h1>

                    <Form v-bind="register ? registerStore.form() : loginStore.form()" :reset-on-success="['password']"
                        v-slot="{ processing }" class="flex flex-col gap-4 text-left">
                        <input id="username" type="text" name="username" value="bob" readonly hidden
                            aria-hidden="true" />

                        <Input id="email" type="email" required autofocus :placeholder="register
                            ? $t('register.emailPlaceholder')
                            : $t('login.emailPlaceholder')" input-class="w-full rounded-xl
                           bg-white dark:bg-black/30
                           border border-black/10 dark:border-white/10
                           px-4 py-3
                           focus:border-cyan-500/60
                           focus:ring-2 focus:ring-cyan-500/30
                           transition">
                            {{ register ? $t('register.emailLabel') : $t('login.emailLabel') }}
                        </Input>

                        <Input v-model="password" id="password" type="password" required :placeholder="register
                            ? $t('register.passwordPlaceholder')
                            : $t('login.passwordPlaceholder')" input-class="w-full rounded-xl
                           bg-white dark:bg-black/30
                           border border-black/10 dark:border-white/10
                           px-4 py-3
                           focus:border-fuchsia-500/60
                           focus:ring-2 focus:ring-fuchsia-500/30
                           transition">
                            {{ register ? $t('register.passwordLabel') : $t('login.passwordLabel') }}
                        </Input>

                        <Input v-if="register" v-model="password2" id="password_confirmation" type="password" required
                            :placeholder="$t('register.confirmPasswordPlaceholder')" input-class="w-full rounded-xl
                           bg-white dark:bg-black/30
                           border border-black/10 dark:border-white/10
                           px-4 py-3
                           focus:border-cyan-500/60
                           focus:ring-2 focus:ring-cyan-500/30
                           transition">
                            {{ $t('register.confirmPasswordLabel') }}
                        </Input>

                        <PrimaryButton type="submit" :disabled="processing" class="group relative mt-6 overflow-hidden rounded-xl
                     bg-linear-to-r from-fuchsia-500 to-cyan-400
                     px-6 py-3 font-medium text-black
                     transition-all duration-300
                     hover:-translate-y-0.5
                     hover:shadow-lg hover:shadow-cyan-400/30
                     disabled:opacity-60 disabled:hover:translate-y-0">
                            <span class="relative z-10">
                                <span v-if="processing">
                                    {{ register
                                        ? $t('register.statusMessage')
                                        : $t('login.statusMessage') }}
                                </span>
                                <span v-else>
                                    {{ register
                                        ? $t('register.mainButton')
                                        : $t('login.mainButton') }}
                                </span>
                            </span>
                        </PrimaryButton>
                    </Form>

                    <span class="mt-6 block text-center text-sm text-neutral-600 dark:text-white/60">
                        {{ register ? $t('register.loginText') : $t('login.signUpText') }}
                        <TextLink @click="() => router.get(register ? '/login' : '/register')" class="ml-1 font-medium
                     text-cyan-600 dark:text-cyan-400
                     hover:text-fuchsia-500 dark:hover:text-fuchsia-400
                     transition-colors">
                            {{ register
                                ? $t('register.loginButton')
                                : $t('login.signUpButton') }}
                        </TextLink>
                    </span>
                </div>
            </div>
        </div>
    </main>
</template>
