<script setup lang="ts">
import Button from "./Button.vue";
import { ref } from "vue";

const email = ref("");
const password = ref("");

const submitLogin = () => {
  console.log("Logging in with:", email.value, password.value);
  // Add your API call or auth logic
};
</script>

<template>
  <div
    class="flex items-center justify-center p-6 min-h-screen 
           bg-white text-black dark:bg-black dark:text-white 
           transition-colors duration-300"
  >
    <div
      class="w-full max-w-sm p-8 rounded-2xl shadow-xl 
             bg-white/70 dark:bg-white/10 
             backdrop-blur-xl border 
             border-black/10 dark:border-white/20 
             transition-colors duration-300"
    >
      <h1 class="text-2xl font-bold mb-6 text-center">Log into MyTunes</h1>

      <form @submit.prevent="submitLogin">
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

        <Button
            class="w-full h-10  
                    bg-white dark:bg-[#181818]
                    hover:bg-pink-500
                    text-black dark:text-white
                    border border-pink-500/50 dark:border-pink-400/40
                    rounded-lg font-semibold 
                    transition-all
                    shadow-sm dark:shadow-lg dark:shadow-pink-500/10"
            @click="submitLogin">
            Log In
        </Button>

      </form>

      <p
        class="mt-4 text-center text-sm 
               text-gray-600 dark:text-gray-400 
               transition-colors duration-300">
        Don't have an account?
        <a href="#" class="text-indigo-600 dark:text-indigo-400 hover:underline">
          Sign up
        </a>
      </p>
    </div>
  </div>
</template>
