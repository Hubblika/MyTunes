<script setup lang="ts">
import ToggleSwitch from 'primevue/toggleswitch';

const props = defineProps<{
  modelValue: boolean
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: boolean): void
}>()
</script>

<template>
    <ToggleSwitch :modelValue="modelValue"
        @update:modelValue="emit('update:modelValue', $event)"
        class="custom-toggle"></ToggleSwitch>
</template>

<style scoped>
.custom-toggle {
    --p-toggleswitch-background: #00ffff;
    --p-toggleswitch-hover-background: #00ffff;
    --p-toggleswitch-checked-background: #ff00ff;
    --p-toggleswitch-checked-hover-background: #ff00ff;
    --p-toggleswitch-handle-background: white;
    z-index: 1 !important;
}
</style>