<template>
  <button
    class="w-full sm:w-auto py-2 px-4 rounded-lg font-medium text-white
           bg-gradient-to-r from-pink-500 via-red-500 to-yellow-400
           hover:brightness-110 focus:outline-none focus:ring-2 focus:ring-pink-400"
  >
    {{ text }}
  </button>
</template>

<script setup>
import { defineProps } from 'vue'

const props = defineProps({
  text: String
})
</script>
