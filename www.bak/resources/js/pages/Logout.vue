<script setup lang="ts">
import { onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import { ApiResult } from '@/types';

onMounted(async () => {
    // TODO: move to ../lib/api.ts
    const data: ApiResult<any> = await fetch('/api/account/logout', {
        method: 'post',
        credentials: 'include'
    }).then(r => r.json());

    if (data.error) {
        alert(`${data.error.name}: ${data.error.message}`);
        return;
    }

    router.get('/login?loggedout=1');
});
</script>

<template>
    <span>Logging out</span>
</template>

