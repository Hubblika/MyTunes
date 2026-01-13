<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { store as songStore } from '@/actions/App/Http/Controllers/SongController';

import { Layout } from '@/layouts';
import { Input, PrimaryButton } from '@/components/common';

const { error } = defineProps<{ error: string }>();
</script>

<template>
    <Layout>
        <div class="w-1/2 mx-auto space-y-2 py-4">
            <!-- TODO: make this look good later -->
            <h1 class="text-xl">Song upload</h1>
            <Form
                v-bind="songStore.form()"
                v-slot="{ processing }"
            >
                <Input id="title" type="text" :tabindex="1" required>title</Input>
                <Input id="date" type="date" :tabindex="2" required>created_at</Input>
                <Input id="duration" type="number" :tabindex="3" required>duration</Input>
                <Input id="genre" type="text" :tabindex="4">genre?</Input>
                <Input id="audio" type="file" :tabindex="5" required>audio (mp3)</Input>
                <Input id="cover" type="file" :tabindex="6" required>cover (jpg)</Input>
                <PrimaryButton type="submit" class="my-4" :tabindex="7" :disabled="processing">
                    <span v-if="processing">Please wait...</span>
                    <span v-else>Submit</span>
                </PrimaryButton>
            </Form>
            <div v-if="!!error">
                <span>{{ error }}</span>
            </div>
        </div>
    </Layout>
</template>
