<script setup lang="ts">
import { ref } from 'vue';
import { Form, router } from '@inertiajs/vue3';
import { Input, PrimaryButton, TextLink } from './common';
import { store as loginStore } from '@/routes/login';
import { store as registerStore } from '@/routes/register';

const {
    register
} = defineProps<{
    register?: boolean
}>();

const password = ref('');
const password2 = ref('');
</script>

<template>
    <div class="flex flex-col items-center justify-center gap-6 p-6 min-h-screen bg-white text-black dark:bg-black dark:text-white">
        <div class="w-full max-w-sm p-8 rounded-2xl shadow-xl
            bg-white/70 dark:bg-white/10
            backdrop-blur-xl border
            border-black/10 dark:border-white/20"
        >
            <h1 class="text-xl font-bold mb-6 text-center">
                {{ register ? $t('register.title') : $t('login.title') }}
            </h1>
            <Form
                v-bind="register ? registerStore.form() : loginStore.form()"
                :reset-on-success="['password']"
                v-slot="{ errors, processing }"
                class="flex flex-col gap-3"
            >
                <input id="username" type="text" name="username" value="bob" class="hidden" required readonly hidden :aria-hidden="true" />
                <Input id="email" type="email" :tabindex="1" autofocus :placeholder="register ? $t('register.emailPlaceholder') : $t('login.emailPlaceholder')" required :pattern="/^[a-z-.]+@([a-z-]+.)+[a-z-]{2,6}$/">{{ register ? $t('register.emailLabel') : $t('login.emailLabel') }}</Input>
                <Input v-model="password" id="password" type="password" :tabindex="2" :placeholder="register ? $t('register.passwordPlaceholder') : $t('login.passwordPlaceholder')" required :pattern="/.{8-16}/">{{ register ? $t('register.passwordLabel') : $t('login.passwordLabel') }}</Input>
                <Input v-if="register" v-model="password2" id="password_confirmation" type="password" :tabindex="3" :placeholder="$t('register.confirmPasswordPlaceholder')" required :pattern="new RegExp(password)">{{ $t('register.confirmPasswordLabel') }}</Input>
                <PrimaryButton type="submit" class="my-4" :tabindex="4" :disabled="processing">
                    <span v-if="processing">{{register ? $t('register.statusMessage') : $t('login.statusMessage') }}</span>
                    <span v-else>{{ register ? $t('register.mainButton') : $t('login.mainButton') }}</span>
                </PrimaryButton>
            </Form>
            <span class="text-center text-sm text-gray-600 dark:text-gray-400">
                {{ register ? $t('register.loginText') : $t('login.signUpText') }}
                <TextLink @click="() => router.get(register ? '/login' : '/register')">
                    {{ register ? $t('register.loginButton') : $t('login.signUpButton') }}
                </TextLink>
            </span>
        </div>
    </div>
</template>
