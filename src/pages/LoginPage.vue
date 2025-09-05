<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
    <div class="flex flex-col md:flex-row items-center md:space-x-10 space-y-6 md:space-y-0 w-full max-w-5xl">

      <!-- Left side image (hidden on mobile) -->
      <div class="hidden md:flex md:flex-1 justify-center">
        <img src="../assets/landing-3x.png" alt="Instagram" class="w-64 md:w-96 h-auto object-cover rounded-lg">
      </div>

      <!-- Right side: form -->
      <div class="flex flex-col bg-white p-6 sm:p-8 rounded-lg shadow-md w-full max-w-md">
        <!-- Logo -->
        <h1 class="text-3xl font-bold text-center mb-6 text-pink-500">Instagram</h1>

        <!-- Login Form -->
        <form @submit.prevent="login" class="space-y-4">
          <InputField id="email" label="Email" type="email" placeholder="Enter your email" v-model="form.email" />
          <InputField id="password" label="Password" type="password" placeholder="Enter your password"
            v-model="form.password" />
          <Button text="Log In" />
        </form>

        <!-- Links -->
        <p class="text-sm text-center mt-4">
          Don't have an account?
          <a href="/" class="text-pink-500 font-medium hover:underline">Sign Up</a>
        </p>

        <!-- Error Message -->
        <p v-if="error" class="text-red-500 text-center mt-2 text-sm">{{ error }}</p>

        <!-- Success Message -->
        <p v-if="success" class="text-green-500 text-center mt-2 text-sm">{{ success }}</p>
      </div>

    </div>
  </div>
</template>

<script setup>
import InputField from '@/components/InputField.vue'
import Button from '@/components/Button.vue'
import { reactive, ref } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'   // <-- import router hook

const router = useRouter()  // <-- get router instance

// Reactive form data
const form = reactive({
  email: '',
  password: ''
})

// Error & success messages
const error = ref('')
const success = ref('')

// Login API call
const login = async () => {
  error.value = ''
  success.value = ''

  try {
    const response = await axios.post('http://localhost:8000/api/login', {
      email: form.email,
      password: form.password
    })

    success.value = 'Login successful!'

    // Store token if returned from Laravel
    if (response.data.access_token) {
      localStorage.setItem('token', response.data.access_token)
    }

    // Store user info (so we can compare ownership later)
    if (response.data.user) {
      localStorage.setItem('user', JSON.stringify(response.data.user))
    }

    // ✅ Redirect to /feed
    router.push('/feed')

  } catch (err) {
    if (err.response && err.response.data.message) {
      error.value = err.response.data.message
    } else if (err.response && err.response.data.errors) {
      error.value = Object.values(err.response.data.errors).flat().join(' ')
    } else {
      error.value = 'Something went wrong.'
    }
  }
}
</script>


<style scoped>
html,
body,
#app {
  height: 100%;
}
</style>
