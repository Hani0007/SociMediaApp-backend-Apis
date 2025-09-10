<template>
  <div class="w-full lg:w-2/3 space-y-6">

    <div v-for="post in posts" :key="post.id" class="bg-white shadow-md rounded-lg p-4 sm:p-6">

      <!-- Post Header -->
      <div class="flex items-center justify-between mb-3">
        <div class="flex items-center space-x-3">
          <div class="p-[2px] rounded-full bg-gradient-to-tr from-yellow-400 via-pink-500 to-purple-600">
            <img
              :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(post.user?.name || 'User')}&background=fff&color=000&size=128`"
              alt="avatar"
              class="w-10 h-10 sm:w-12 sm:h-12 rounded-full object-cover bg-white"
            />
          </div>
          <p class="font-semibold text-gray-800 text-sm sm:text-base">{{ post.user?.name || "User" }}</p>
        </div>

        <!-- Post Options -->
        <div v-if="currentUser && post.user?.id === currentUser.id" class="relative post-dropdown">
          <button @click="toggleOptions(post.id)" class="text-gray-500 hover:text-gray-700 font-bold text-xl">⋯</button>
          <div v-if="openPostId === post.id"
               class="absolute right-0 mt-2 w-32 bg-white border rounded-lg shadow-lg z-10">
            <button @click="startEditPost(post)" class="block w-full text-left px-4 py-2 hover:bg-gray-100">Edit</button>
            <button @click="$emit('delete-post', post)" class="block w-full text-left px-4 py-2 hover:bg-gray-100 text-red-500">Delete</button>
          </div>
        </div>
      </div>

      <!-- Post Description -->
      <div v-if="editPostId === post.id">
        <textarea v-model="editDescription"
                  class="w-full border rounded-lg p-2 focus:outline-none focus:ring focus:ring-blue-300 mb-3"></textarea>
        <div class="flex justify-end gap-2">
          <button @click="cancelEdit" class="px-3 py-1 bg-gray-300 rounded-lg hover:bg-gray-400">Cancel</button>
          <button @click="saveEditPost(post)"
                  class="px-3 py-1 bg-green-500 text-white rounded-lg hover:bg-green-600">Save</button>
        </div>
      </div>
      <p v-else class="text-gray-700 mb-3 text-sm sm:text-base">{{ post.description }}</p>

      <!-- Post Media -->
      <div v-if="post.media?.length" class="space-y-3">
        <div v-for="file in post.media" :key="file.id">
          <img v-if="file.media_type === 'image'" :src="getMediaUrl(file.url)"
               class="w-full rounded-lg object-cover h-64 sm:h-80 md:h-96 lg:h-[600px]" />
          <video v-else-if="file.media_type === 'video'" controls class="w-full rounded-lg max-h-[600px]">
            <source :src="getMediaUrl(file.url)" type="video/mp4" />
          </video>
        </div>
      </div>

      <!-- Like & Comment Section -->
      <div class="flex items-center gap-4 mt-3 border-t pt-2">
        <button @click="$emit('toggle-like', post)"
                class="flex items-center gap-1 px-3 py-1 rounded-lg hover:bg-gray-100"
                :class="post.liked ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-700'">
          <span v-if="post.liked">❤️</span>
          <span v-else>🤍</span>
          <span>{{ post.likes_count || 0 }}</span>
        </button>

        <button @click="post.showCommentBox = !post.showCommentBox"
                class="flex items-center gap-1 px-3 py-1 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
          💬 Comment ({{ post.comments?.length || 0 }})
        </button>
      </div>

      <!-- Likes Text -->
      <p v-if="post.likes_count > 0" class="text-sm text-gray-600 mt-1">
        <span v-if="post.liked">You<span v-if="post.likes_count > 1"> and {{ post.likes_count - 1 }} others</span> like this</span>
        <span v-else>{{ post.like_users[0]?.name || 'Someone' }}<span v-if="post.likes_count > 1"> and {{ post.likes_count - 1 }} others</span> like this</span>
      </p>

      <!-- Comment Box -->
      <div v-if="post.showCommentBox" class="mt-2">
        <div class="flex gap-2 mt-2">
          <input v-model="post.newComment" type="text" placeholder="Write a comment..."
                 class="flex-1 border rounded-lg p-2 focus:outline-none focus:ring focus:ring-blue-300" />
          <button @click="$emit('submit-comment', post)"
                  class="px-3 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Post</button>
        </div>

        <div v-if="post.comments?.length" class="mt-2 space-y-2">
          <div v-for="comment in post.comments" :key="comment.id"
               class="border p-2 rounded-lg flex justify-between items-center bg-gray-50">
            <div>
              <span class="font-semibold">{{ comment.user.name }}:</span>
              <span v-if="editCommentId !== comment.id">{{ comment.text }}</span>
              <input v-else v-model="editCommentText" class="border rounded p-1 text-sm" />
            </div>
            <div v-if="comment.user?.id === currentUser.id" class="flex gap-2 text-sm">
              <button v-if="editCommentId !== comment.id" @click="startEditComment(comment)"
                      class="text-blue-500 hover:underline">Edit</button>
              <button v-if="editCommentId === comment.id" @click="saveEditComment(post, comment)"
                      class="text-green-500 hover:underline">Save</button>
              <button @click="$emit('delete-comment', post, comment)"
                      class="text-red-500 hover:underline">Delete</button>
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- Empty States -->
    <p v-if="error" class="text-center text-red-500 text-sm sm:text-base">{{ error }}</p>
    <p v-if="!error && posts.length === 0" class="text-center text-gray-500 text-sm sm:text-base">
      No posts available yet.
    </p>

  </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
  posts: Array,
  currentUser: Object,
  error: String
})

const emit = defineEmits([
  'delete-post',
  'toggle-like',
  'submit-comment',
  'update-comment',
  'update-post',
  'delete-comment'
])

const openPostId = ref(null)
const editPostId = ref(null)
const editDescription = ref("")
const editCommentId = ref(null)
const editCommentText = ref("")

const getMediaUrl = (path) => path?.startsWith("http") ? path : `http://localhost:8000/storage/${path}`

const toggleOptions = (postId) => openPostId.value = openPostId.value === postId ? null : postId

const startEditPost = (post) => {
  editPostId.value = post.id
  editDescription.value = post.description||""
  openPostId.value = null
}

const cancelEdit = () => {
  editPostId.value = null
  editDescription.value = ""
}

const saveEditPost = (post) => {
  const desc = editDescription.value?.trim()

  console.log("🔥 saveEditPost fired with:", desc)
  // update local immediately
  post.description = desc

  // reset edit state
  cancelEdit()

  // emit to parent for API update
  emit("update-post", post, desc)
}



const startEditComment = (comment) => {
  editCommentId.value = comment.id
  editCommentText.value = comment.text
}

const saveEditComment = (post, comment) => {
  if (!editCommentText.value.trim()) {
    alert("Comment cannot be empty")
    return
  }
  // update the comment text locally
  comment.text = editCommentText.value
  emit("update-comment", post, comment)

  // reset state
  editCommentId.value = null
  editCommentText.value = ""
}
</script>
