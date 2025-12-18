<script setup lang="ts">
import ToggleSwitch from 'primevue/toggleswitch';
</script>

<template>
    <ToggleSwitch class="custom-toggle"></ToggleSwitch>
</template>

<style scoped>
.custom-toggle {
    --p-toggleswitch-background: #00ffff;
    --p-toggleswitch-hover-background: #00ffff;
    --p-toggleswitch-checked-background: #ff00ff;
    --p-toggleswitch-checked-hover-background: #ff00ff;
    --p-toggleswitch-handle-background: white;  
}
</style>