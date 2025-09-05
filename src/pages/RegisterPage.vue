<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
    <div class="flex flex-col md:flex-row items-center md:space-x-10 space-y-6 md:space-y-0 max-w-5xl w-full">

      <!-- Left side image (hidden on mobile) -->
      <div class="hidden md:flex md:flex-1 justify-center">
        <img src="../assets/landing-3x.png" alt="Instagram" class="w-full h-auto object-cover rounded-lg">
      </div>

      <!-- Right side: form -->
      <div class="flex flex-col md:flex-1 bg-white p-6 sm:p-8 rounded-lg shadow-md w-full max-w-md">
        <h1 class="text-3xl font-bold text-center mb-6 text-pink-500">Instagram</h1>

        <form @submit.prevent="register" class="space-y-4">
          <InputField id="name" label="Full Name" placeholder="Enter your full name" v-model="form.name" />
          <InputField id="email" label="Email" type="email" placeholder="Enter your email" v-model="form.email" />
          <InputField id="password" label="Password" type="password" placeholder="Enter your password"
            v-model="form.password" />
          <InputField id="confirmPassword" label="Confirm Password" type="password" placeholder="Confirm your password"
            v-model="form.confirmPassword" />

          <Button text="Sign Up" />
        </form>

        <p class="text-sm text-center mt-4">
          Already have an account?
          <a href="/login" class="text-pink-500 font-medium hover:underline">Log In</a>
        </p>

        <!-- Error Message -->
        <p v-if="error" class="text-red-500 text-center mt-2">{{ error }}</p>

        <!-- Success Message -->
        <p v-if="success" class="text-green-500 text-center mt-2">{{ success }}</p>
      </div>

    </div>
  </div>
</template>

<script setup>
import InputField from '@/components/InputField.vue'
import Button from '@/components/Button.vue'
import { reactive, ref } from 'vue'
import axios from 'axios'

// Reactive form state matching backend
const form = reactive({
  name: '',
  email: '',
  password: '',
  confirmPassword: ''
})

const error = ref('')
const success = ref('')

// Submit registration
const register = async () => {
  error.value = ''
  success.value = ''

  if (form.password !== form.confirmPassword) {
    error.value = "Passwords do not match!"
    return
  }

  try {
    const response = await axios.post('http://localhost:8000/api/register', {
      name: form.name,
      email: form.email,
      password: form.password,
      confirmPassword: form.confirmPassword
    })

    success.value = "Registration successful! You can now log in."
    Object.keys(form).forEach(key => form[key] = '') // reset form

  } catch (err) {
    if (err.response && err.response.data.errors) {
      error.value = Object.values(err.response.data.errors).flat().join(' ')
    } else if (err.response && err.response.data.message) {
      error.value = err.response.data.message
    } else {
      error.value = "Something went wrong."
    }
  }
}
</script>
