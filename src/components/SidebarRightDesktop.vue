<template>
  <div class="hidden lg:block w-1/3 px-6">
    <div class="sticky top-20 space-y-6">
      <!-- Profile info -->
      <div class="flex items-center space-x-3">
        <div class="p-[2px] rounded-full bg-gradient-to-tr from-yellow-400 via-pink-500 to-purple-600">
          <img :src="userAvatar" alt="your avatar"
            class="w-12 h-12 rounded-full object-cover bg-white" />
        </div>
        <div>
          <p class="font-semibold">{{ userName }}</p>
          <p class="text-sm text-gray-500">{{ userTag }}</p>
        </div>
      </div>

      <!-- Suggestions for you -->
      <div>
        <p class="text-gray-500 font-semibold mb-3">Suggestions for you</p>
        <div v-for="suggestion in suggestions" :key="suggestion.id" class="flex items-center justify-between mb-3">
          <div class="flex items-center space-x-3">
            <div class="p-[2px] rounded-full bg-gradient-to-tr from-yellow-400 via-pink-500 to-purple-600">
              <img :src="suggestion.avatar" alt="suggestion" class="w-10 h-10 rounded-full object-cover bg-white" />
            </div>
            <p class="text-sm font-semibold">{{ suggestion.name }}</p>
          </div>
          <button class="text-blue-500 text-sm font-semibold">Follow</button>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
const userName = "Abdul Hanan"
const userTag = "Hanan"
const userAvatar = "https://ui-avatars.com/api/?name=You&background=0D8ABC&color=fff"

// Generate 5 dummy suggestions
const suggestions = Array.from({ length: 5 }, (_, i) => ({
  id: i + 1,
  name: `user${i + 1}`,
  avatar: `https://ui-avatars.com/api/?name=User${i + 1}&background=random`
}))
</script>
