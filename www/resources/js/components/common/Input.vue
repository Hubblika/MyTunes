<script setup lang="ts">
    const {
        id,
        type = 'text',
        tabindex,
        autofocus,
        placeholder,
        required,
        pattern,
        error
    } = defineProps<{
        id: string,
        type?: string,
        tabindex?: number,
        autofocus?: boolean,
        placeholder?: string,
        required?: boolean,
        pattern?: RegExp,
        error?: string
    }>();

    const model = defineModel();
</script>

<template>
    <label class="space-y-1">
        <span class="font-semibold text-cyan-500 block">
            <slot></slot>
        </span>
        <input
            v-model="model"
            :id
            :type
            :name="id"
            :tabindex
            class="
                text-black dark:text-white bg-gray-100 dark:bg-[#111]
                w-full rounded-lg p-3 border border-black/10 dark:border-white/10
                outline-2 focus:outline-indigo-400 dark:focus:outline-pink-500 user-invalid:outline-red-500
            "
            :autofocus
            :autocomplete="type === 'email' ? type : undefined"
            :placeholder
            :required
            :pattern="pattern ? String(pattern).slice(1, -1) : undefined"
        />
    </label>
</template>
