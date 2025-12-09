<script setup lang="ts">
import Button from "./Button.vue";
import { ref } from "vue";

const email = ref("");
const password = ref("");
const isLogin = ref(true);

const submitLogin = async () => {
  try {
    const res = await fetch("/api/account/login", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      credentials: "include",
      body: JSON.stringify({
        email: email.value,
        password: password.value,
      }),
    });

    const data = await res.json();

    if (!res.ok) {
      console.error("Login failed:", data);
      alert(`Error: ${data.error?.message || data.error?.name || "Unknown error"}`);
      return;
    }

    console.log("Login successful:", data.data);
    window.location.href = '/';
  } catch (err) {
    console.error("Network error:", err);
    alert("Network error, please try again later.");
  }
};


const submitSignup = async () => {
  try {
    const res = await fetch("/api/account/register", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        email: email.value,
        password: password.value,
      }),
    });

    const data = await res.json();

    if (!res.ok) {
      console.error("Signup failed:", data);
      alert(`Error: ${data.message || "Unknown error"}`);
      return;
    }

    console.log("Signup successful:", data);
    window.location.href = '/';
  } catch (err) {
    console.error("Network error:", err);
    alert("Network error, please try again later.");
  }
};


const toggleMode = () => {
  isLogin.value = !isLogin.value;
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
            required
          />
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
            placeholder="Enter your password"
            required
          />
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
          type="submit"
        >
          {{ isLogin ? "Log In" : "Sign Up" }}
        </Button>
      </form>

      <p
        class="mt-4 text-center text-sm 
               text-gray-600 dark:text-gray-400 
               transition-colors duration-300"
      >
        {{ isLogin ? "Don't have an account?" : "Already have an account?" }}
        <a href="#" 
           class="text-indigo-600 dark:text-indigo-400 hover:underline"
           @click.prevent="toggleMode">
          {{ isLogin ? "Sign up" : "Log in" }}
        </a>
      </p>
    </div>
  </div>
</template>
