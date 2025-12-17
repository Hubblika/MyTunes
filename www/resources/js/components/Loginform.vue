<script setup lang="ts">
import { ref, useTemplateRef } from 'vue';
import { Form, router } from '@inertiajs/vue3';
import { PrimaryButton } from './common';
import type { ApiResult } from '@/lib/types';
import { describeError } from '@/lib/utils';
// import { store as loginStore } from '@/routes/login';
import { store as registerStore } from '@/routes/register';

const email = ref('');
const password = ref('');
const password2 = ref('');
const isLogin = ref(true);

const errorElement = useTemplateRef('errorElement');
const showError = ref(false);

const submitLogin = async () => {
    try {
        const data: ApiResult<any> = await fetch('/api/account/login', {
            method: 'post',
            headers: {
                'Content-Type': 'application/json',
                'accept': 'application/json'
            },
            credentials: "include",
            body: JSON.stringify({
                email: email.value,
                password: password.value,
            })
        }).then(r => r.json());

        if (data.error) {
            setError(data.error.message || describeError(data.error.name));
            return;
        }
        router.get('/');
    } catch {
        setError('Network error');
    }
};

const submitSignup = async () => {
    try {
        const data: ApiResult<any> = await fetch('/api/account/register', {
            method: 'post',
            headers: {
                'Content-Type': 'application/json',
                'accept': 'application/json'
            },
            body: JSON.stringify({
                email: email.value,
                password: password.value,
            })
        }).then(r => r.json());

        if (data.error) {
            setError(data.error.message || describeError(data.error.name));
            return;
        }

        router.get('/');
    } catch {
        setError('Network error');
    }
};


const toggleMode = () => {
    isLogin.value = !isLogin.value;
};

function setError(error: string) {
    if (!errorElement.value) {
        alert(error);
        return;
    }

    showError.value = true;
    errorElement.value.textContent = error;
    setTimeout(() => {
        showError.value = false;
        setTimeout(() => {
            errorElement.value!.textContent = '';
        }, 1200);
    }, 10000);
}
</script>

<template>
    <div class="flex flex-col items-center justify-center gap-6 p-6 min-h-screen
        bg-white text-black dark:bg-black dark:text-white
        transition-colors duration-300"
    >
        <div class="w-full max-w-sm p-8 rounded-2xl shadow-xl
            bg-white/70 dark:bg-white/10
            backdrop-blur-xl border
            border-black/10 dark:border-white/20
            transition-colors duration-300"
        >
            <h1 class="text-2xl font-bold mb-6 text-center">
                {{ isLogin ? "Log into MyTunes" : "Sign Up for MyTunes" }}
            </h1>
            <Form
                v-bind="registerStore.form()"
                :reset-on-success="['password']"
                v-slot="{ errors, processing }"
            >
                <div class="mb-4">
                    <input id="username" type="text" name="username" value="bob" class="hidden" required readonly hidden :aria-hidden="true" />
                    <label for="email" class="block mb-1 font-semibold text-cyan-500">Email</label>
                    <input
                        v-model="email"
                        id="email"
                        type="email"
                        name="email"
                        :tabindex="1"
                        class="
                            w-full p-3 rounded-lg
                            bg-gray-100 dark:bg-[#111]
                            text-black dark:text-white
                            border border-black/10 dark:border-white/10
                            focus:outline-none
                            focus:ring-2 focus:ring-indigo-400
                            dark:focus:ring-pink-500
                            transition-colors duration-300
                        "
                        autofocus
                        autocomplete="email"
                        placeholder="Enter your email"
                        required
                    />
                </div>

                <div class="mb-1">
                    <label for="password" class="block mb-1 font-semibold text-cyan-500">Password</label>
                    <input
                        v-model="password"
                        id="password"
                        type="password"
                        name="password"
                        :tabindex="2"
                        class="
                            w-full p-3 rounded-lg
                            bg-gray-100 dark:bg-[#111]
                            text-black dark:text-white
                            border border-black/10 dark:border-white/10
                            focus:outline-none
                            focus:ring-2 focus:ring-indigo-400
                            dark:focus:ring-pink-500
                            transition-colors duration-300
                        "
                        placeholder="Enter your password"
                        required
                    />
                </div>
                <div class="mb-6">
                    <input
                        v-model="password2"
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        :tabindex="3"
                        class="
                            w-full p-3 rounded-lg
                            bg-gray-100 dark:bg-[#111]
                            text-black dark:text-white
                            border border-black/10 dark:border-white/10
                            focus:outline-none
                            focus:ring-2 focus:ring-indigo-400
                            dark:focus:ring-pink-500
                            transition-colors duration-300
                        "
                        placeholder="Confirm your password"
                        required
                    />
                </div>

                <PrimaryButton type="submit" :tabindex="4">{{ isLogin ? "Log In" : "Sign Up" }}</PrimaryButton>
            </Form>
            <p class="
                mt-4 text-center text-sm
                text-gray-600 dark:text-gray-400
                transition-colors duration-300
            ">
                {{ isLogin ? "Don't have an account?" : "Already have an account?" }}
                <a href="#"
                    class="text-indigo-600 dark:text-indigo-400 hover:underline"
                    @click.prevent="toggleMode"
                >
                    {{ isLogin ? "Sign up" : "Log in" }}
                </a>
            </p>
        </div>
    </div>
    <span :class="['font-semibold text-white text-sm bg-red-600 rounded-md px-3.5 py-2.5 shadow-md flex justify-center items-center fixed bottom-6 left-1/2 -translate-1/2 transition-opacity pointer-events-none', { 'opacity-0 duration-1000': !showError }]" ref="errorElement"></span>
</template>
