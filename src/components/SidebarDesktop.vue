<template>
  <div class="hidden md:flex fixed top-0 left-0 h-full w-64 bg-white border-r shadow-md flex-col p-4 z-20">
    <h1 class="text-xl font-bold mb-8 text-pink-500">Instagram</h1>

    <nav class="space-y-4 flex-1">
      <a href="#" class="flex items-center space-x-3 text-gray-700 hover:text-black">
        <span>🏠</span>
        <span>Home</span>
      </a>
      <a href="#" class="flex items-center space-x-3 text-gray-700 hover:text-black">
        <span>🔍</span>
        <span>Search</span>
      </a>
      <a href="#" class="flex items-center space-x-3 text-gray-700 hover:text-black" @click.prevent="openModal">
        <span>➕</span>
        <span>Create</span>
      </a>
      <a href="#" class="flex items-center space-x-3 text-gray-700 hover:text-black">
        <span>❤️</span>
        <span>Notifications</span>
      </a>
      <a href="#" class="flex items-center space-x-3 text-gray-700 hover:text-black">
        <span>👤</span>
        <span>Profile</span>
      </a>
    </nav>

    <!-- Logout Button -->
    <button @click="logout"
      class="flex items-center space-x-2 px-3 py-2 bg-gray-100 rounded-md text-gray-700 text-sm font-medium hover:bg-gray-200 hover:text-pink-600 transition">
      <span>⏻</span>
      <span>Logout</span>
    </button>
  </div>
</template>

<script setup>
import { defineEmits } from 'vue'

const emit = defineEmits(['openModal', 'logout'])

const openModal = () => emit('openModal')
const logout = () => emit('logout')
</script>
