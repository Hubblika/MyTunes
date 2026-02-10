<script setup lang="ts">
import { ref, watch } from 'vue'
import { Button } from '.'

const props = defineProps({
    modelValue: { type: Boolean, required: true },
    inputValue: { type: String, required: true },
    title: { type: String, required: true },
    placeholder: { type: String, default: 'Enter value...' }
})

const emit = defineEmits<{
    (e: 'update:modelValue', value: boolean): void
    (e: 'update:inputValue', value: string): void
    (e: 'save'): void
    (e: 'cancel'): void
}>()

const localInput = ref(props.inputValue)

watch(() => props.inputValue, (newVal) => {
    localInput.value = newVal
})

function save() {
    emit('update:inputValue', localInput.value)
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
            <h2 class="text-lg font-semibold mb-4">
                {{ props.title }}
            </h2>

            <input v-model="localInput" type="text" :placeholder="props.placeholder" class="w-full px-4 py-2 mb-4
               rounded-xl
               bg-white/20 dark:bg-black/20
               backdrop-blur-md
               border border-black/10 dark:border-white/10
               placeholder-black/50 dark:placeholder-white/50
               focus:outline-none
               focus:ring-2 focus:ring-cyan-500/50" />

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
