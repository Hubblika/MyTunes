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
            <h1 class="text-2xl font-bold mb-6 text-center">
                {{ register ? 'Sign Up for MyTunes' : 'Log into MyTunes' }}
            </h1>
            <Form
                v-bind="register ? registerStore.form() : loginStore.form()"
                :reset-on-success="['password']"
                v-slot="{ errors, processing }"
                class="flex flex-col gap-3"
            >
                <input id="username" type="text" name="username" value="bob" class="hidden" required readonly hidden :aria-hidden="true" />
                <Input id="email" type="email" :tabindex="1" autofocus placeholder="Enter your email" required :pattern="/^[a-z-.]+@([a-z-]+.)+[a-z-]{2,6}$/">Email</Input>
                <Input v-model="password" id="password" type="password" :tabindex="2" placeholder="Enter your password" required>Password</Input>
                <Input v-if="register" v-model="password2" id="password_confirmation" type="password" :tabindex="3" placeholder="Enter your password" required :pattern="new RegExp(password)">Password</Input>
                <PrimaryButton type="submit" class="my-4" :tabindex="4" :disabled="processing">
                    <span v-if="processing">Please wait...</span>
                    <span v-else>{{ register ? 'Sign Up' : 'Log in' }}</span>
                </PrimaryButton>
            </Form>
            <span class="text-center text-sm text-gray-600 dark:text-gray-400">
                {{ register ? 'Already have an account?' : 'Don\'t have an account?' }}
                <TextLink @click="() => router.get(register ? '/login' : '/register')">
                    {{ register ? 'Sign up' : 'Log in' }}
                </TextLink>
            </span>
        </div>
    </div>
</template>
