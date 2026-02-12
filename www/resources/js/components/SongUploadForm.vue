<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { Layout } from '@/layouts'
import { Input, PrimaryButton } from '@/components/common'

const form = useForm({
    title: '',
    artist: '',
    album: '',
    date: '',
    duration: '',
    genre: '',
    audio: null as File | null,
    cover: null as File | null,
})

function submit() {
    if (!form.audio) {
        form.setError('audio', 'Please select an audio file.')
        return
    }

    if (!form.cover) {
        form.setError('cover', 'Please select a cover image.')
        return
    }

    form.post('/songs', {
        forceFormData: true,
        onSuccess: () => {
            alert('Song uploaded successfully!')
            form.reset()
        },
        onError: (errors) => {
            console.error('Validation errors:', errors)
        }
    })
}
</script>

<template>
    <main class="relative w-full text-neutral-900 dark:text-white">
        <div class="relative z-10 flex max-h-screen items-center justify-center p-6">
            <div class="flex min-w-md flex-col gap-6">

                <div class="w-full rounded-2xl
                    border border-black/10 dark:border-white/10
                    bg-white/80 dark:bg-white/5
                    backdrop-blur-xl
                    px-10 py-10
                    shadow-xl dark:shadow-2xl
                    shadow-black/10 dark:shadow-black/40">

                    <h1 class="mb-8 text-2xl font-semibold tracking-tight text-center">
                        <span class="bg-linear-to-r from-fuchsia-500 to-cyan-500
                            bg-clip-text text-transparent">
                            {{ $t('admin.uploadTitle') }}
                        </span>
                    </h1>

                    <form @submit.prevent="submit" class="flex flex-col gap-5 text-left">

                        <Input v-model="form.title" id="title" type="text" required>
                            {{ $t('admin.titleLabel') }}
                        </Input>
                        <div v-if="form.errors.title" class="text-sm text-red-500">{{ form.errors.title }}</div>

                        <Input v-model="form.artist" id="artist" type="text" required>
                            {{ $t('admin.artistLabel') }}
                        </Input>
                        <div v-if="form.errors.artist" class="text-sm text-red-500">{{ form.errors.artist }}</div>

                        <Input v-model="form.album" id="album" type="text" required>
                            {{ $t('admin.albumLabel') }}
                        </Input>
                        <div v-if="form.errors.album" class="text-sm text-red-500">{{ form.errors.album }}</div>

                        <Input v-model="form.date" id="date" type="date" required>
                            {{ $t('admin.releaseDateLabel') }}
                        </Input>
                        <div v-if="form.errors.date" class="text-sm text-red-500">{{ form.errors.date }}</div>

                        <Input v-model="form.duration" id="duration" type="number" required>
                            {{ $t('admin.DurationLabel') }}
                        </Input>
                        <div v-if="form.errors.duration" class="text-sm text-red-500">{{ form.errors.duration }}
                        </div>

                        <Input v-model="form.genre" id="genre" type="text">
                            {{ $t('admin.genreLabel') }}
                        </Input>
                        <div v-if="form.errors.genre" class="text-sm text-red-500">{{ form.errors.genre }}</div>

                        <div class="flex flex-col gap-1">
                            <label class="cursor-pointer rounded-xl
                                            border border-black/10 dark:border-white/10
                                            bg-white dark:bg-black/30
                                            px-4 py-3 text-left
                                            hover:bg-black/5 dark:hover:bg-white/10
                                            transition">
                                {{ form.audio ? form.audio.name : $t('admin.chooseAudio') }}
                                <input type="file" accept=".mp3" class="hidden"
                                    @change="e => form.audio = (e.target as HTMLInputElement).files?.[0] ?? null" />
                            </label>

                            <div v-if="form.errors.audio" class="text-sm text-red-500">
                                {{ form.errors.audio }}
                            </div>
                        </div>
                        
                        <div class="flex flex-col gap-1">
                            <label class="cursor-pointer rounded-xl
                                            border border-black/10 dark:border-white/10
                                            bg-white dark:bg-black/30
                                            px-4 py-3 text-left
                                            hover:bg-black/5 dark:hover:bg-white/10
                                            transition">
                                {{ form.cover ? form.cover.name : $t('admin.chooseCover') }}
                                <input type="file" accept="image/*" class="hidden"
                                    @change="e => form.cover = (e.target as HTMLInputElement).files?.[0] ?? null" />
                            </label>

                            <div v-if="form.errors.cover" class="text-sm text-red-500">
                                {{ form.errors.cover }}
                            </div>
                        </div>


                        <PrimaryButton type="submit" :disabled="form.processing" class="mt-6">
                            <span v-if="form.processing">{{ $t('admin.uploading') }}</span>
                            <span v-else>{{ $t('admin.submitButton') }}</span>
                        </PrimaryButton>
                    </form>
                </div>
            </div>
        </div>
    </main>
</template>