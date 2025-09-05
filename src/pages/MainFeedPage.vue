<template>
  <div class="min-h-screen bg-gray-100 flex flex-col md:flex-row">

    <SidebarDesktop @openModal="openModal" @logout="logout" />



    <!-- 🔹 Main Content -->
    <div class="flex-1 md:ml-64 px-4 sm:px-6 pb-20 md:pb-6">
      <div class="max-w-6xl mx-auto flex flex-col lg:flex-row gap-8 py-6">

        <!-- 🔹 Feed Section -->
        <div class="w-full lg:w-2/3 space-y-6">
          <div v-for="post in posts" :key="post.id" class="bg-white shadow-md rounded-lg p-4 sm:p-6">

            <!-- Post header -->
            <div class="flex items-center justify-between mb-3">
              <!-- 🔹 User avatar + name -->
              <div class="flex items-center space-x-3">
                <div class="p-[2px] rounded-full bg-gradient-to-tr from-yellow-400 via-pink-500 to-purple-600">
                  <img
                    :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(post.user?.name || 'User')}&background=fff&color=000&size=128`"
                    alt="avatar" class="w-10 h-10 sm:w-12 sm:h-12 rounded-full object-cover bg-white" />
                </div>
                <p class="font-semibold text-gray-800 text-sm sm:text-base">
                  {{ post.user?.name || "User" }}
                </p>
              </div>

              <!-- 🔹 Three dots menu (only visible if currentUser owns the post) -->
              <div v-if="currentUser && post.user?.id === currentUser.id" class="relative post-dropdown"
                @click.stop="toggleOptions(post.id)">
                <button class="text-gray-500 hover:text-gray-700 font-bold text-xl">⋯</button>

                <!-- Dropdown -->
                <div v-if="openPostId === post.id"
                  class="absolute right-0 mt-2 w-32 bg-white border rounded-lg shadow-lg z-10">
                  <button @click="editPost(post)" class="block w-full text-left px-4 py-2 hover:bg-gray-100">
                    Edit
                  </button>
                  <button @click="deletePost(post)"
                    class="block w-full text-left px-4 py-2 hover:bg-gray-100 text-red-500">
                    Delete
                  </button>
                </div>
              </div>
            </div>

            <!-- Post description -->
            <div v-if="editPostId === post.id">
              <textarea v-model="editDescription"
                class="w-full border rounded-lg p-2 focus:outline-none focus:ring focus:ring-blue-300 mb-3"></textarea>
              <div class="flex justify-end gap-2">
                <button @click="cancelEdit" class="px-3 py-1 bg-gray-300 rounded-lg hover:bg-gray-400">Cancel</button>
                <button @click="updatePost(post)"
                  class="px-3 py-1 bg-green-500 text-white rounded-lg hover:bg-green-600">Save</button>
              </div>
            </div>
            <p v-else class="text-gray-700 mb-3 text-sm sm:text-base">{{ post.description }}</p>

            <!-- Post media -->
            <div v-if="post.media && post.media.length" class="space-y-3">
              <div v-for="file in post.media" :key="file.id">
                <img v-if="file.media_type === 'image'" :src="getMediaUrl(file.url)" alt="post media"
                  class="w-full rounded-lg object-cover h-64 sm:h-80 md:h-96 lg:h-[600px]" />
                <video v-else-if="file.media_type === 'video'" controls class="w-full rounded-lg max-h-[600px]">
                  <source :src="getMediaUrl(file.url)" type="video/mp4" />
                  Your browser does not support the video tag.
                </video>
              </div>
            </div>

            <!-- 🔹 Like and Comment Section -->
            <div class="flex items-center gap-4 mt-3 border-t pt-2">
              <button @click="toggleLike(post)" class="flex items-center gap-1 px-3 py-1 rounded-lg hover:bg-gray-100"
                :class="post.liked ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-700'">
                <span v-if="post.liked">❤️</span>
                <span v-else>🤍</span>
                <span>{{ post.likes_count || 0 }}</span>
              </button>

              <button @click="toggleCommentBox(post)"
                class="flex items-center gap-1 px-3 py-1 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                💬 Comment ({{ post.comments?.length || 0 }})
              </button>
            </div>
            <!-- Comment Box -->
            <div v-if="post.showCommentBox" class="mt-2">
              <!-- Input for new comment -->
              <div class="flex gap-2 mt-2">
                <input v-model="post.newComment" type="text" placeholder="Write a comment..."
                  class="flex-1 border rounded-lg p-2 focus:outline-none focus:ring focus:ring-blue-300" />
                <button @click="submitComment(post)"
                  class="px-3 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                  Post
                </button>
              </div>

              <!-- Render comments -->
              <div v-if="post.comments && post.comments.length" class="mt-2 space-y-2">
                <div v-for="comment in post.comments" :key="comment.id"
                  class="border p-2 rounded-lg flex justify-between items-center bg-gray-50">
                  <!-- Comment text -->
                  <div>
                    <span class="font-semibold">{{ comment.user.name }}:</span>
                    <span v-if="editCommentId !== comment.id">{{ comment.text }}</span>
                    <input v-else v-model="editCommentText" class="border rounded p-1 text-sm" />
                  </div>

                  <!-- Actions (only show if current user is comment owner) -->
                  <div v-if="comment.user?.id === currentUser.id" class="flex gap-2 text-sm">
                    <button v-if="editCommentId !== comment.id" @click="startEditComment(comment)"
                      class="text-blue-500 hover:underline">
                      Edit
                    </button>
                    <button v-if="editCommentId === comment.id" @click="updateComment(post, comment)"
                      class="text-green-500 hover:underline">
                      Save
                    </button>
                    <button @click="deleteComment(post, comment)" class="text-red-500 hover:underline">
                      Delete
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Empty states -->
          <p v-if="error" class="text-center text-red-500 text-sm sm:text-base">{{ error }}</p>
          <p v-if="!error && posts.length === 0" class="text-center text-gray-500 text-sm sm:text-base">No posts
            available yet.</p>
        </div>

        <!-- 🔹 Right Sidebar (Desktop Only) -->
       <SidebarRightDesktop />

      </div>
    </div>

    <!-- 🔹 Mobile Bottom Navigation -->
       <MobileBottomNav @openModal="openModal" @logout="logout" />


    <!-- 🔹 Toast Message -->
    <div v-if="showToast"
      class="fixed bottom-16 md:bottom-4 right-4 bg-gray-800 text-white px-4 py-2 rounded shadow-lg z-50 transition-all">
      {{ toastMessage }}
    </div>
  </div>

  <!-- 🔹 Modal -->
  <CreatePostModal :isOpen="showModal" @close="showModal = false" @shared="handleShared" />

</template>
<script setup>
import SidebarDesktop from '@/components/SidebarDesktop.vue'
import SidebarRightDesktop from '@/components/SidebarRightDesktop.vue'
import MobileBottomNav from '@/components/MobileBottomNav.vue'
import { ref, onMounted, onBeforeUnmount } from "vue"
import axios from "axios"
import CreatePostModal from "@/components/CreatePostModal.vue"
import { useRouter } from "vue-router"
const router = useRouter()

const posts = ref([])
const error = ref("")
const showModal = ref(false)
const openPostId = ref(null)
const editPostId = ref(null)
const editDescription = ref("")
const editCommentId = ref(null)
const editCommentText = ref("")
const toastMessage = ref("")
const showToast = ref(false)
// 🔹 This makes currentUser reactive and available in template
const currentUser = ref(null)

const displayToast = (message, duration = 2000) => {
  toastMessage.value = message
  showToast.value = true
  setTimeout(() => { showToast.value = false }, duration)
}

const getMediaUrl = (path) => path?.startsWith("http") ? path : `http://localhost:8000/storage/${path}`
const openModal = () => showModal.value = true

const handleShared = (postData) => {
  const user = JSON.parse(localStorage.getItem("user") || "{}")
  const newPost = {
    ...postData,
    user: postData.user || { name: user.name || "You" },
    liked: false,
    likes_count: postData.likes_count || 0,
    comments: postData.comments || [],
    showCommentBox: false,
    newComment: ""
  }
  posts.value.unshift(newPost)
  showModal.value = false
}

const toggleOptions = (postId) => { openPostId.value = openPostId.value === postId ? null : postId }
const editPost = (post) => { editPostId.value = post.id; editDescription.value = post.description; openPostId.value = null }
const cancelEdit = () => { editPostId.value = null; editDescription.value = "" }

const updatePost = async (post) => {
  if (!editDescription.value.trim()) return alert("Description cannot be empty")
  try {
    const token = localStorage.getItem("token")
    const res = await axios.put(
      `http://localhost:8000/api/posts/${post.id}`,
      { description: editDescription.value },
      { headers: { Authorization: `Bearer ${token}` } }
    )
    const index = posts.value.findIndex(p => p.id === post.id)
    if (index !== -1) posts.value[index].description = res.data.post.description
    cancelEdit()
  } catch (err) { alert("Failed to update post"); console.error(err) }
}

const deletePost = async (post) => {
  if (!confirm("Are you sure you want to delete this post?")) return
  try {
    const token = localStorage.getItem("token")
    await axios.delete(`http://localhost:8000/api/posts/${post.id}`, { headers: { Authorization: `Bearer ${token}` } })
    posts.value = posts.value.filter(p => p.id !== post.id)
    openPostId.value = null
  } catch (err) { alert("Failed to delete post"); console.error(err) }
}

const toggleLike = async (post) => {
  try {
    const token = localStorage.getItem("token")
    if (!post.liked) {
      const res = await axios.post("http://127.0.0.1:8000/api/like", { post_id: post.id }, { headers: { Authorization: `Bearer ${token}` } })
      post.liked = true
      post.likes_count = (post.likes_count || 0) + 1
      post.like_id = res.data.like.id
    } else {
      if (!post.like_id) return
      await axios.delete(`http://127.0.0.1:8000/api/like/${post.like_id}`, { headers: { Authorization: `Bearer ${token}` } })
      post.liked = false
      post.likes_count = (post.likes_count || 1) - 1
      post.like_id = null
    }
  } catch (err) { console.error(err); displayToast("Failed to toggle like ❌") }
}

const toggleCommentBox = (post) => { post.showCommentBox = !post.showCommentBox }

const submitComment = async (post) => {
  if (!post.newComment?.trim()) return
  try {
    const token = localStorage.getItem("token")
    await axios.post(`http://127.0.0.1:8000/api/posts/${post.id}/comments`, { post_id: post.id, comment_text: post.newComment }, { headers: { Authorization: `Bearer ${token}` } })
    const res = await axios.get(`http://127.0.0.1:8000/api/posts/${post.id}/comments`, { headers: { Authorization: `Bearer ${token}` } })
    post.comments = res.data.comments.map(c => ({ id: c.id, text: c.comment_text, user: c.user }))
    post.newComment = ""
    displayToast("Comment added successfully ✅")
  } catch (err) { console.error(err); displayToast("Failed to add comment ❌") }
}

const startEditComment = (comment) => { editCommentId.value = comment.id; editCommentText.value = comment.text }
const updateComment = async (post, comment) => {
  if (!editCommentText.value.trim()) return
  try {
    const token = localStorage.getItem("token")
    await axios.put(`http://127.0.0.1:8000/api/comments/${comment.id}`, { comment_text: editCommentText.value }, { headers: { Authorization: `Bearer ${token}` } })
    const cIndex = post.comments.findIndex(c => c.id === comment.id)
    if (cIndex !== -1) post.comments[cIndex].text = editCommentText.value
    editCommentId.value = null
    editCommentText.value = ""
    displayToast("Comment updated ✅")
  } catch (err) { console.error(err); displayToast("Failed to update comment ❌") }
}

const deleteComment = async (post, comment) => {
  if (!confirm("Delete this comment?")) return
  try {
    const token = localStorage.getItem("token")
    await axios.delete(`http://127.0.0.1:8000/api/comments/${comment.id}`, { headers: { Authorization: `Bearer ${token}` } })
    post.comments = post.comments.filter(c => c.id !== comment.id)
    displayToast("Comment deleted ✅")
  } catch (err) { console.error(err); displayToast("Failed to delete comment ❌") }
}

const handleClickOutside = (event) => {
  const dropdowns = document.querySelectorAll(".post-dropdown")
  let clickedInside = false
  dropdowns.forEach(el => { if (el.contains(event.target)) clickedInside = true })
  if (!clickedInside) openPostId.value = null
}

onMounted(async () => {
  document.addEventListener("click", handleClickOutside)
  const token = localStorage.getItem("token")
  if (!token) {
    error.value = "No token found. Please login again."
    return
  }

  const user = JSON.parse(localStorage.getItem("user") || "{}")
  if (user && user.id) {
    currentUser.value = user
  }

  try {
    const res = await axios.get("http://localhost:8000/api/allposts", {
      headers: { Authorization: `Bearer ${token}` }
    })

    const postsWithLikesAndComments = await Promise.all(
      res.data.map(async (post) => {
        // Fetch likes
        const likesRes = await axios.get(
          `http://127.0.0.1:8000/api/posts/${post.id}/likes`,
          { headers: { Authorization: `Bearer ${token}` } }
        )

        let liked = false
        let like_id = null
        if (post.likes?.length) {
          const userLike = post.likes.find(like => like.user_id === user.id)
          if (userLike) { liked = true; like_id = userLike.id }
        }

        // Fetch comments
        const commentsRes = await axios.get(
          `http://127.0.0.1:8000/api/posts/${post.id}/comments`,
          { headers: { Authorization: `Bearer ${token}` } }
        )

        const comments = commentsRes.data.comments.map(c => ({
          id: c.id,
          text: c.comment_text,
          user: c.user
        }))

        return {
          ...post,
          liked,
          like_id,
          likes_count: likesRes.data.likes_count || 0,
          comments: comments,
          comments_count: comments.length,
          showCommentBox: false,
          newComment: ""
        }
      })
    )

    posts.value = postsWithLikesAndComments
  } catch (err) {
    error.value = "Failed to load posts"
    console.error(err)
  }
})


const logout = () => {
  localStorage.removeItem('token')
  router.push('/login')

}
onBeforeUnmount(() => document.removeEventListener("click", handleClickOutside))
</script>
