<script setup lang="ts">
import { Input, PrimaryButton } from '@/components/common';
import { useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

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

const inputStyles = "w-full rounded-xl bg-white dark:bg-black/30 border border-black/10 dark:border-white/10 px-4 py-3 focus:border-cyan-500/60 focus:ring-2 focus:ring-cyan-500/30 transition"

function submit() {
    form.clearErrors();
    let hasError = false;

    if (!form.title) { form.setError('title', 'admin.validation.required'); hasError = true; }
    if (!form.artist) { form.setError('artist', 'admin.validation.required'); hasError = true; }
    if (!form.album) { form.setError('album', 'admin.validation.required'); hasError = true; }
    if (!form.date) { form.setError('date', 'admin.validation.required'); hasError = true; }

    const durationValue = Number(form.duration);
    if (!form.duration || durationValue <= 0) {
        form.setError('duration', 'admin.validation.invalidDuration');
        hasError = true;
    }

    if (!form.audio) { form.setError('audio', 'admin.validation.audioRequired'); hasError = true; }
    if (!form.cover) { form.setError('cover', 'admin.validation.coverRequired'); hasError = true; }

    if (hasError) return;

    form.post('/songs', {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            alert(t('admin.songUploadSuccess'));
            form.reset();
        },
        onError: (errors) => {
            console.error('Server-side validation failed:', errors);
        }
    });
}
</script>

<template>
    <main class="relative w-full min-h-screen overflow-hidden text-neutral-900 dark:text-white">
        <div class="relative z-10 flex min-h-screen items-center justify-center p-4 sm:p-6">
            <div class="w-full max-w-md lg:max-w-2xl xl:max-w-3xl">
                <div
                    class="rounded-2xl border border-black/10 dark:border-white/10 bg-white/80 dark:bg-white/5 backdrop-blur-xl shadow-xl dark:shadow-2xl shadow-black/10 dark:shadow-black/40 px-5 py-6 sm:px-8 sm:py-8 md:px-10 md:py-10 max-h-[90vh] flex flex-col">

                    <h1 class="mb-6 sm:mb-8 text-xl sm:text-2xl font-semibold tracking-tight text-center shrink-0">
                        <span class="bg-linear-to-r from-fuchsia-500 to-cyan-500 bg-clip-text text-transparent">
                            {{ $t('admin.uploadTitle') }}
                        </span>
                    </h1>

                    <form @submit.prevent="submit" novalidate
                        class="flex flex-col p-2 gap-4 sm:gap-5 text-left min-h-0 overflow-y-auto custom-scrollbar">

                        <div class="flex flex-col gap-1">
                            <Input v-model="form.title" id="title" type="text" :placeholder="$t('admin.titleLabel')"
                                :input-class="inputStyles">
                                {{ $t('admin.titleLabel') }}
                            </Input>
                            <span v-if="form.errors.title"
                                class="ml-1 text-[11px] font-bold uppercase tracking-wider text-red-500 animate-in fade-in slide-in-from-top-1">
                                {{ $t(form.errors.title) }}
                            </span>
                        </div>

                        <div class="flex flex-col gap-1">
                            <Input v-model="form.artist" id="artist" type="text" :placeholder="$t('admin.artistLabel')"
                                :input-class="inputStyles">
                                {{ $t('admin.artistLabel') }}
                            </Input>
                            <span v-if="form.errors.artist"
                                class="ml-1 text-[11px] font-bold uppercase tracking-wider text-red-500 animate-in fade-in slide-in-from-top-1">
                                {{ $t(form.errors.artist) }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex flex-col gap-1">
                                <Input v-model="form.album" id="album" type="text" :placeholder="$t('admin.albumLabel')"
                                    :input-class="inputStyles">
                                    {{ $t('admin.albumLabel') }}
                                </Input>
                                <span v-if="form.errors.album"
                                    class="ml-1 text-[11px] font-bold uppercase tracking-wider text-red-500 animate-in fade-in slide-in-from-top-1">
                                    {{ $t(form.errors.album) }}
                                </span>
                            </div>

                            <div class="flex flex-col gap-1">
                                <Input v-model="form.genre" id="genre" type="text" :placeholder="$t('admin.genreLabel')"
                                    :input-class="inputStyles">
                                    {{ $t('admin.genreLabel') }}
                                </Input>
                                <span v-if="form.errors.genre"
                                    class="ml-1 text-[11px] font-bold uppercase tracking-wider text-red-500 animate-in fade-in slide-in-from-top-1">
                                    {{ $t(form.errors.genre) }}
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex flex-col gap-1">
                                <Input v-model="form.date" id="date" type="date" :input-class="inputStyles">
                                    {{ $t('admin.releaseDateLabel') }}
                                </Input>
                                <span v-if="form.errors.date"
                                    class="ml-1 text-[11px] font-bold uppercase tracking-wider text-red-500 animate-in fade-in slide-in-from-top-1">
                                    {{ $t(form.errors.date) }}
                                </span>
                            </div>

                            <div class="flex flex-col gap-1">
                                <Input v-model="form.duration" id="duration" type="number"
                                    :placeholder="$t('admin.DurationLabel')" :input-class="inputStyles">
                                    {{ $t('admin.DurationLabel') }}
                                </Input>
                                <span v-if="form.errors.duration"
                                    class="ml-1 text-[11px] font-bold uppercase tracking-wider text-red-500 animate-in fade-in slide-in-from-top-1">
                                    {{ $t(form.errors.duration) }}
                                </span>
                            </div>
                        </div>

                        <div class="flex flex-col gap-1">
                            <label
                                class="w-full cursor-pointer rounded-xl border border-black/10 dark:border-white/10 bg-white dark:bg-black/30 px-4 py-3 text-left text-sm sm:text-base hover:bg-black/5 dark:hover:bg-white/10 transition">
                                <span class="block text-xs mb-1 opacity-60">{{ $t('admin.audioFileLabel') }}</span>
                                <span class="font-medium truncate block">
                                    {{ form.audio ? form.audio.name : $t('admin.chooseAudio') }}
                                </span>
                                <input type="file" accept=".mp3" class="hidden"
                                    @change="e => form.audio = (e.target as HTMLInputElement).files?.[0] ?? null" />
                            </label>
                            <span v-if="form.errors.audio"
                                class="ml-1 text-[11px] font-bold uppercase tracking-wider text-red-500 animate-in fade-in slide-in-from-top-1">
                                {{ $t(form.errors.audio) }}
                            </span>
                        </div>

                        <div class="flex flex-col gap-1">
                            <label
                                class="w-full cursor-pointer rounded-xl border border-black/10 dark:border-white/10 bg-white dark:bg-black/30 px-4 py-3 text-left text-sm sm:text-base hover:bg-black/5 dark:hover:bg-white/10 transition">
                                <span class="block text-xs mb-1 opacity-60">{{ $t('admin.coverFileLabel') }}</span>
                                <span class="font-medium truncate block">
                                    {{ form.cover ? form.cover.name : $t('admin.chooseCover') }}
                                </span>
                                <input type="file" accept="image/*" class="hidden"
                                    @change="e => form.cover = (e.target as HTMLInputElement).files?.[0] ?? null" />
                            </label>
                            <span v-if="form.errors.cover"
                                class="ml-1 text-[11px] font-bold uppercase tracking-wider text-red-500 animate-in fade-in slide-in-from-top-1">
                                {{ $t(form.errors.cover) }}
                            </span>
                        </div>

                        <PrimaryButton type="submit" :disabled="form.processing" class="mt-2 sm:mt-4 w-full shrink-0">
                            <span v-if="form.processing">{{ $t('admin.uploading') }}</span>
                            <span v-else>{{ $t('admin.submitButton') }}</span>
                        </PrimaryButton>

                    </form>
                </div>
            </div>
        </div>
    </main>
</template>