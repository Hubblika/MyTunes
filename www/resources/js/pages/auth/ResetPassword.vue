<script setup lang="ts">
import { Input, PrimaryButton } from '@/components/common';
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { Layout } from '@/layouts';

defineOptions({
    layout: Layout,
})

const props = defineProps<{
    email: string
    token: string
}>();

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const logoSrc = ref('');

function updateLogo() {
    const isDark = document.documentElement.classList.contains('dark');
    logoSrc.value = isDark
        ? '/uploads/logos/logo_dark.svg'
        : '/uploads/logos/logo_light.svg';
}

let observer: MutationObserver | null = null;

onMounted(() => {
    updateLogo();
    observer = new MutationObserver(updateLogo);
    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['class'],
    });
});

onBeforeUnmount(() => {
    observer?.disconnect();
})

const submit = () => {
    form.post('/reset-password', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
}
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
                            {{ $t('forgotPassword.title') }}
                        </span>
                    </h1>

                    <form @submit.prevent="submit" class="flex flex-col gap-4 text-left">
                        <div class="flex flex-col gap-1">
                            <Input v-model="form.email" id="email" type="email" disabled
                                :placeholder="$t('login.emailPlaceholder')">
                                {{ $t('login.emailLabel') }}
                            </Input>
                            <span v-if="form.errors.email" class="ml-1 text-[11px] font-bold uppercase text-red-500">
                                {{ $t(form.errors.email) }}
                            </span>
                        </div>

                        <div class="flex flex-col gap-1">
                            <Input v-model="form.password" id="password" type="password" required autofocus
                                :placeholder="$t('login.passwordPlaceholder')">
                                {{ $t('login.passwordLabel') }}
                            </Input>
                            <span v-if="form.errors.password" class="ml-1 text-[11px] font-bold uppercase text-red-500">
                                {{ $t(form.errors.password) }}
                            </span>
                        </div>

                        <div class="flex flex-col gap-1">
                            <Input v-model="form.password_confirmation" id="password_confirmation" type="password"
                                required :placeholder="$t('register.confirmPasswordPlaceholder')">
                                {{ $t('register.confirmPasswordLabel') }}
                            </Input>
                        </div>

                        <PrimaryButton type="submit" :disabled="form.processing" class="mt-4">
                            <span class="relative z-10">
                                {{ form.processing ? $t('forgotPassword.sending') : $t('forgotPassword.submitButton') }}
                            </span>
                        </PrimaryButton>
                    </form>
                </div>
            </div>
        </div>
    </main>
</template>