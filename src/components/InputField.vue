<template>
  <div class="mb-4">
    <label :for="id" class="block text-gray-700 font-medium mb-1">{{ label }}</label>
    <input
      :id="id"
      :type="type"
      :placeholder="placeholder"
      v-model="model"
      class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
    />
  </div>
</template>

<script setup>
import { defineProps, defineModel } from 'vue'

const props = defineProps({
  id: String,
  label: String,
  type: {
    type: String,
    default: 'text'
  },
  placeholder: String
})

const model = defineModel() // works as v-model from parent
</script>
