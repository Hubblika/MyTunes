<script setup lang="ts">
const emit = defineEmits<{
  (e: 'update:modelValue', value: number): void
}>()

defineProps<{
  modelValue: number
  max: number
}>()
</script>

<template>
    <input
    type="range"
    min="0"
    :max="max"
    :value="modelValue"
    class="w-full h-1 rounded-lg bg-gray-700 accent-pink-500 cursor-pointer"
    @input="emit('update:modelValue', Number(($event.target as HTMLInputElement).value))"/>
</template>
