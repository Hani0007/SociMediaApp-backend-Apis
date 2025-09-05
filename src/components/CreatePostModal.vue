<template>
  <!-- Overlay -->
  <div
    v-if="isOpen"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
  >
    <div class="bg-white rounded-lg shadow-lg w-96 p-6 relative">
      <!-- Step 1: File Upload -->
      <div
        v-if="step === 1"
        class="flex flex-col items-center justify-center space-y-6"
      >
        <h2 class="text-lg font-bold">Upload Media</h2>
        <input type="file" multiple @change="handleFiles" class="block" />

        <!-- Preview -->
        <div
          v-if="previewUrls.length"
          class="mt-4 grid grid-cols-2 gap-2"
        >
          <img
            v-for="(url, idx) in previewUrls"
            :key="idx"
            :src="url"
            alt="Preview"
            class="rounded-lg max-h-32 object-cover"
          />
        </div>

        <div class="flex justify-between w-full mt-6">
          <button
            @click="close"
            class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400"
          >
            Cancel
          </button>
          <button
            @click="uploadMedia"
            class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600"
            :disabled="!files.length || uploading"
          >
            {{ uploading ? "Uploading..." : "Next" }}
          </button>
        </div>
      </div>

      <!-- Step 2: Add Description -->
      <div
        v-else-if="step === 2"
        class="flex flex-col items-center justify-center space-y-6"
      >
        <h2 class="text-lg font-bold">Add Description</h2>
        <textarea
          v-model="description"
          placeholder="Add Description..."
          class="w-full border rounded-lg p-2 focus:outline-none focus:ring focus:ring-blue-300"
          rows="4"
        ></textarea>

        <div class="flex justify-between w-full mt-6">
          <button
            @click="step = 1"
            class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400"
          >
            Back
          </button>
          <button
            @click="createPost"
            class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600"
            :disabled="sharing"
          >
            {{ sharing ? "Sharing..." : "Share" }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue"
import axios from "axios"

const props = defineProps({
  isOpen: Boolean,
})
const emit = defineEmits(["close", "shared"])

const step = ref(1)
const files = ref([])
const previewUrls = ref([])
const description = ref("")
const uploadedMediaIds = ref([])

const uploading = ref(false)
const sharing = ref(false)

const handleFiles = (e) => {
  files.value = Array.from(e.target.files)
  previewUrls.value = files.value.map((f) => URL.createObjectURL(f))
}

const uploadMedia = async () => {
  if (!files.value.length) return alert("Please select files")

  uploading.value = true
  uploadedMediaIds.value = []

  try {
    const token = localStorage.getItem("token")
    if (!token) {
      alert("You must be logged in!")
      return
    }

    for (let file of files.value) {
      const formData = new FormData()
      formData.append("media_type", "image")
      formData.append("file", file)

      const mediaRes = await axios.post(
        "http://localhost:8000/api/media",
        formData,
        {
          headers: {
            "Content-Type": "multipart/form-data",
            Authorization: `Bearer ${token}`,
          },
        }
      )

      uploadedMediaIds.value.push(mediaRes.data.id)
    }

    step.value = 2
  } catch (err) {
    console.error("Upload error:", err.response?.data || err.message)
    alert("Failed to upload media ❌")
  } finally {
    uploading.value = false
  }
}

const createPost = async () => {
  if (!description.value) return alert("Please add a description")

  sharing.value = true
  try {
    const token = localStorage.getItem("token")
    if (!token) {
      alert("You must be logged in!")
      return
    }

    const res = await axios.post(
      "http://localhost:8000/api/posts",
      {
        description: description.value,
        media_ids: uploadedMediaIds.value,
      },
      {
        headers: { Authorization: `Bearer ${token}` },
      }
    )

    emit("shared", res.data.post)
  } catch (err) {
    console.error("Share error:", err.response?.data || err.message)
    alert("Failed to share post ❌")
  } finally {
    sharing.value = false
    close() // ✅ always reset & close modal
  }
}

const close = () => {
  step.value = 1
  files.value = []
  previewUrls.value = []
  description.value = ""
  uploadedMediaIds.value = []
  uploading.value = false
  sharing.value = false
  emit("close")
}
</script>
