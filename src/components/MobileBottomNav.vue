<template>
  <div class="fixed bottom-0 left-0 w-full bg-white border-t shadow-md flex justify-around md:hidden py-2 z-30">
    <button class="flex flex-col items-center text-gray-700 hover:text-black">
      <span>🏠</span>
      <span class="text-xs">Home</span>
    </button>

    <button class="flex flex-col items-center text-gray-700 hover:text-black">
      <span>🔍</span>
      <span class="text-xs">Search</span>
    </button>

    <button @click.prevent="openModal" class="flex flex-col items-center text-gray-700 hover:text-black">
      <span>➕</span>
      <span class="text-xs">Create</span>
    </button>

    <button class="flex flex-col items-center text-gray-700 hover:text-black">
      <span>❤️</span>
      <span class="text-xs">Notifications</span>
    </button>

    <button class="flex flex-col items-center text-gray-700 hover:text-black">
      <span>👤</span>
      <span class="text-xs">Profile</span>
    </button>

    <!-- Logout button -->
    <button @click="logout" class="flex flex-col items-center text-red-500 hover:text-red-700">
      <span>⏻</span>
      <span class="text-xs">Logout</span>
    </button>
  </div>
</template>

<script setup>
const emit = defineEmits(["openModal", "logout"])

const openModal = () => {
  emit("openModal")
}

const logout = () => {
  emit("logout")
}
</script>
