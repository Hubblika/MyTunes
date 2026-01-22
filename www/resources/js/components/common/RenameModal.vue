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
  <div v-if="props.modelValue" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-500/6 dark:text-white">
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-lg p-6 w-96">
      <h2 class="text-lg font-bold mb-4">{{ props.title }}</h2>
      <input v-model="localInput" type="text"
             :placeholder="props.placeholder"
             class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded mb-4 focus:outline-none focus:ring-2 focus:ring-cyan-500 dark:focus:ring-cyan-400" />

      <div class="flex justify-end gap-2">
        <Button class="bg-pink-500 hover:bg-pink-600 text-white rounded-full px-4 py-2"
                @click="cancel">
          {{ $t('generic.cancel') }}
        </Button>
        <Button class="bg-cyan-500 hover:bg-cyan-600 text-white rounded-full px-4 py-2"
                @click="save">
          {{ $t('generic.save') }}
        </Button>
      </div>
    </div>
  </div>
</template>
