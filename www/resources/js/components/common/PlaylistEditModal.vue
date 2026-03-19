<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { Button, Icon } from '.'

const props = defineProps({
    modelValue: { type: Boolean, required: true },
    title: { type: String, required: true },
    nameValue: { type: String, required: true },
    descriptionValue: { type: String, default: '' },
    coverUrl: { type: String, default: '' }
})

const emit = defineEmits<{
    (e: 'update:modelValue', value: boolean): void
    (e: 'update:nameValue', value: string): void
    (e: 'update:descriptionValue', value: string): void
    (e: 'update:coverFile', value: File | null): void
    (e: 'save'): void
    (e: 'cancel'): void
}>()

const localName = ref(props.nameValue)
const localDescription = ref(props.descriptionValue)
const localCover = ref<File | null>(null)
const coverPreview = ref<string | null>(props.coverUrl)

const MAX_NAME_LENGTH = 50
const MAX_DESCRIPTION_LENGTH = 200
const WARNING_THRESHOLD = 10

watch(() => props.nameValue, (newVal) => localName.value = newVal)
watch(() => props.descriptionValue, (newVal) => localDescription.value = newVal)
watch(() => props.coverUrl, (newVal) => coverPreview.value = newVal)

watch(localName, (val) => {
    if (val.length > MAX_NAME_LENGTH) localName.value = val.slice(0, MAX_NAME_LENGTH)
})
watch(localDescription, (val) => {
    if (val.length > MAX_DESCRIPTION_LENGTH) localDescription.value = val.slice(0, MAX_DESCRIPTION_LENGTH)
})

const nameCharsLeft = computed(() => MAX_NAME_LENGTH - localName.value.length)
const descriptionCharsLeft = computed(() => MAX_DESCRIPTION_LENGTH - localDescription.value.length)

const nameCounterColor = computed(() => {
    if (nameCharsLeft.value <= 0) return 'text-red-500'
    if (nameCharsLeft.value <= WARNING_THRESHOLD) return 'text-yellow-400'
    return 'text-gray-500 dark:text-gray-400'
})

const descriptionCounterColor = computed(() => {
    if (descriptionCharsLeft.value <= 0) return 'text-red-500'
    if (descriptionCharsLeft.value <= WARNING_THRESHOLD) return 'text-yellow-400'
    return 'text-gray-500 dark:text-gray-400'
})

function onCoverSelected(e: Event) {
    const file = (e.target as HTMLInputElement).files?.[0] ?? null
    localCover.value = file
    if (file) coverPreview.value = URL.createObjectURL(file)
}

function save() {
    emit('update:nameValue', localName.value)
    emit('update:descriptionValue', localDescription.value)
    emit('update:coverFile', localCover.value)
    emit('save')
    emit('update:modelValue', false)
}

function cancel() {
    emit('cancel')
    emit('update:modelValue', false)
}
</script>

<template>
    <div v-if="props.modelValue" class="fixed inset-0 z-50 flex items-center justify-center
         bg-black/10 backdrop-blur-sm">
        <div class="w-96 p-6
         text-black dark:text-white
         bg-white/20 dark:bg-black/20
         backdrop-blur-md
         border border-black/10 dark:border-white/10
         rounded-2xl shadow-lg">

            <h2 class="text-lg font-semibold mb-6">
                {{ props.title }}
            </h2>

            <div class="flex gap-4 mb-4">
                <div class="relative w-32 h-32 rounded-lg overflow-hidden shrink-0">
                    <img :src="coverPreview || '/uploads/thumbnails/defaultThumbnail.png'"
                        class="w-full h-full object-cover rounded-lg shadow-md" />
                    <input type="file" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer"
                        @change="onCoverSelected" />
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <Icon name="pencil" class="size-5 text-white bg-black/40 rounded-full p-1" />
                    </div>
                </div>

                <div class="flex-1 flex flex-col">
                    <input v-model="localName" type="text" :placeholder="$t('playlistEditModal.playlistName')" :maxlength="MAX_NAME_LENGTH"
                        class="w-full px-4 py-2 rounded-xl bg-white/20 dark:bg-black/20
                     backdrop-blur-md border border-black/10 dark:border-white/10
                     placeholder-black/50 dark:placeholder-white/50 focus:outline-none
                     focus:ring-2 focus:ring-cyan-500/50" />
                    <p :class="nameCounterColor" class="text-xs mt-1">
                        {{ nameCharsLeft }} {{ $t('modal.charactersLeft') }}
                    </p>
                </div>
            </div>

            <textarea v-model="localDescription" :placeholder="$t('playlistEditModal.description')" :maxlength="MAX_DESCRIPTION_LENGTH" class="w-full px-4 py-2 mb-4 rounded-xl bg-white/20 dark:bg-black/20
                 backdrop-blur-md border border-black/10 dark:border-white/10
                 placeholder-black/50 dark:placeholder-white/50 focus:outline-none
                 focus:ring-2 focus:ring-cyan-500/50 resize-none h-28"></textarea>
            <p :class="descriptionCounterColor" class="text-xs mb-4">
                {{ descriptionCharsLeft }} {{ $t('modal.charactersLeft') }}
            </p>

            <div class="flex justify-end gap-2">
                <Button class="px-4 py-2 rounded-full
             bg-white/20 dark:bg-black/20
             hover:bg-white/30 dark:hover:bg-black/30
             backdrop-blur-md
             border border-black/10 dark:border-white/10" @click="cancel">
                    {{ $t('generic.cancel') }}
                </Button>

                <Button class="px-4 py-2 rounded-full
             bg-cyan-500/80 hover:bg-cyan-500
             text-white shadow-lg" @click="save">
                    {{ $t('generic.save') }}
                </Button>
            </div>

        </div>
    </div>
</template>
