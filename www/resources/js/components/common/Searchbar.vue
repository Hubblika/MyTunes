<script setup lang="ts">
import { ref, watch } from 'vue';
import { Icon } from '.';

const props = defineProps<{
  placeholder: string
  modelValue?: string
}>();

const emit = defineEmits(['update:modelValue']);

const query = ref(props.modelValue || '');

watch(query, (val) => {
  emit('update:modelValue', val);
});
</script>

<template>
    <label class="w-3/4 h-11 border border-black dark:border-white rounded-full flex
           transition-all duration-200 hover:border-black/60 dark:hover:border-white/80
           shadow-sm hover:shadow-md">
        <div
            class="h-full aspect-square flex justify-center items-center transition-transform duration-200 hover:scale-110 hover:text-black/70 dark:hover:text-white/70">
            <Icon name="search" class="text-black dark:text-white size-5 translate-x-0.5"></Icon>
        </div>

        <input id="search" type="text" class="font-semibold text-black dark:text-white placeholder-gray-600 dark:placeholder-gray-300
                 h-full outline-none grow placeholder:font-semibold
                 transition-colors duration-200 focus:text-black/90 dark:focus:text-white/90"
            :placeholder="placeholder" v-model="query" autocomplete="off" />

        <button :class="[
            'h-full aspect-square flex justify-center items-center cursor-pointer transition-transform duration-200',
            { 'collapse': !query.length }
        ]" @click="query = ''" :style="{ transform: query.length ? 'scale(1)' : 'scale(0)' }">
            <Icon name="send"
                class="size-5 -translate-x-0.5 hover:rotate-12 hover:text-blue-600 dark:hover:text-blue-400 transition-transform duration-200" />
        </button>
    </label>
</template>
