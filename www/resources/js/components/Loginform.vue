<script setup lang="ts">
import { ref, useTemplateRef } from 'vue';
import { router } from '@inertiajs/vue3';
import { PrimaryButton } from './common';
import type { ApiResult } from '@/lib/types';
import { describeError } from '@/lib/utils';

const email = ref('');
const password = ref('');
const isLogin = ref(true);

const errorElement = useTemplateRef('errorElement');
const showError = ref(false);

const submitLogin = async () => {
    console.log("fasz")
    try {
        const data: ApiResult<any> = await fetch("/api/account/login", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
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
        const data: ApiResult<any> = await fetch("/api/account/register", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
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
            <form @submit.prevent="isLogin ? submitLogin() : submitSignup()">
                <div class="mb-4">
                <label class="block mb-1 font-semibold text-cyan-500">Email</label>
                <input
                    v-model="email"
                    type="email"
                    class="w-full p-3 rounded-lg
                            bg-gray-100 dark:bg-[#111]
                            text-black dark:text-white
                            border border-black/10 dark:border-white/10
                            focus:outline-none
                            focus:ring-2 focus:ring-indigo-400
                            dark:focus:ring-pink-500
                            transition-colors duration-300"
                    placeholder="Enter your email"
                    required/>
                </div>

                <div class="mb-6">
                <label class="block mb-1 font-semibold text-cyan-500">Password</label>
                <input
                    v-model="password"
                    type="password"
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

                <PrimaryButton type="submit">{{ isLogin ? "Log In" : "Sign Up" }}</PrimaryButton>
            </form>
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
