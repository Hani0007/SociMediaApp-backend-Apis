<template>
  <div class="min-h-screen bg-gray-100 flex flex-col md:flex-row">

    <SidebarDesktop @openModal="openModal" @logout="logout" />

    <!-- Main Content -->
    <div class="flex-1 md:ml-64 px-4 sm:px-6 pb-20 md:pb-6">
      <div class="max-w-6xl mx-auto flex flex-col lg:flex-row gap-8 py-6">

        <FeedSection 
          :posts="posts" 
          :currentUser="currentUser" 
          :error="error"
          @edit-post="editPost"
          @update-post="updatePost"
          @delete-post="deletePost"
          @toggle-like="toggleLike"
          @submit-comment="submitComment"
          @update-comment="updateComment"
          @delete-comment="deleteComment"
        />



        <!-- Right Sidebar -->
        <SidebarRightDesktop />
      </div>
    </div>

    <MobileBottomNav @openModal="openModal" @logout="logout" />

    <!-- Toast -->
    <div v-if="showToast"
      class="fixed bottom-16 md:bottom-4 right-4 bg-gray-800 text-white px-4 py-2 rounded shadow-lg z-50 transition-all">
      {{ toastMessage }}
    </div>
    <CreatePostModal :isOpen="showModal" @close="showModal = false" @shared="handleShared" />

  </div>

  <div class="flex justify-center gap-4 mt-4">
    <button @click="fetchPosts(currentPage - 1)" :disabled="currentPage === 1"
      class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 disabled:opacity-50">
      Previous
    </button>

    <span class="px-4 py-2 bg-gray-200 rounded">
      Page {{ currentPage }} of {{ lastPage }}
    </span>

    <button @click="fetchPosts(currentPage + 1)" :disabled="currentPage === lastPage"
      class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 disabled:opacity-50">
      Next
    </button>
  </div>

</template>

<script setup>
import SidebarDesktop from '@/components/SidebarDesktop.vue'
import SidebarRightDesktop from '@/components/SidebarRightDesktop.vue'
import MobileBottomNav from '@/components/MobileBottomNav.vue'
import CreatePostModal from "@/components/CreatePostModal.vue"
import FeedSection from '@/components/FeedSection.vue'
import { ref, onMounted, onBeforeUnmount } from "vue"
import { useRouter } from "vue-router"

// ✅ Import API methods
import {
  fetchPostsApi,
  updatePostApi,
  deletePostApi,
  likePostApi,
  unlikePostApi,
  addCommentApi,
  updateCommentApi,
  deleteCommentApi
} from "@/api/api"

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
const currentUser = ref(null)

const displayToast = (message, duration = 2000) => {
  toastMessage.value = message
  showToast.value = true
  setTimeout(() => { showToast.value = false }, duration)
}

const openModal = () => showModal.value = true

const handleShared = (postData) => {
  const user = JSON.parse(localStorage.getItem("user") || "{}")
  posts.value.unshift({
    ...postData,
    user: postData.user || { id: user.id, name: user.name || "You" },
    liked: false,
    likes_count: 0,
    like_users: [],
    comments: [],
    showCommentBox: false,
    newComment: ""
  })
  showModal.value = false
}

// -------------------- POSTS -------------------- //
const toggleOptions = (postId) => openPostId.value = openPostId.value === postId ? null : postId
const editPost = (post) => { editPostId.value = post.id; editDescription.value = post.description; openPostId.value = null }
const cancelEdit = () => { editPostId.value = null; editDescription.value = "" }

const updatePost = async (post, desc) => {
  const trimmed = desc?.trim()
  if (!trimmed || trimmed.length === 0) {
    console.warn("⚠️ Description cannot be empty")
    return
  }

  try {
    const res = await updatePostApi(post.id, trimmed)
    post.description = res.data.post.description
    cancelEdit()
  } catch (err) {
    console.error("❌ Failed to update post", err)
  }
}

const deletePost = async (post) => {
  if (!confirm("Are you sure?")) return
  try {
    await deletePostApi(post.id)
    posts.value = posts.value.filter(p => p.id !== post.id)
    openPostId.value = null
  } catch {
    alert("Failed to delete post")
  }
}

// -------------------- LIKES -------------------- //
const toggleLike = async (post) => {
  try {
    if (!post.liked) {
      const res = await likePostApi(post.id)
      post.liked = true
      post.likes_count++
      post.like_id = res.data.like.id
      post.like_users = [currentUser.value, ...post.like_users.filter(u => u.id !== currentUser.value.id)]
    } else {
      if (!post.like_id) return
      await unlikePostApi(post.like_id)
      post.liked = false
      post.likes_count = Math.max(0, post.likes_count - 1)
      post.like_id = null
      post.like_users = post.like_users.filter(u => u.id !== currentUser.value.id)
    }
  } catch {
    displayToast("Failed to toggle like ❌")
  }
}

// -------------------- COMMENTS -------------------- //
const toggleCommentBox = (post) => post.showCommentBox = !post.showCommentBox

const submitComment = async (post) => {
  if (!post.newComment?.trim()) return
  try {
    const res = await addCommentApi(post.id, post.newComment)
    post.comments.push({ id: res.data.comment.id, text: res.data.comment.comment_text, user: currentUser.value })
    post.newComment = ""
    displayToast("Comment added ✅")
  } catch {
    displayToast("Failed to add comment ❌")
  }
}

const startEditComment = (comment) => { editCommentId.value = comment.id; editCommentText.value = comment.text }

const updateComment = async (post, comment) => {
  if (!editCommentText.value.trim()) return
  try {
    await updateCommentApi(comment.id, editCommentText.value)
    comment.text = editCommentText.value
    editCommentId.value = null
    editCommentText.value = ""
    displayToast("Comment updated ✅")
  } catch {
    displayToast("Failed to update comment ❌")
  }
}

const deleteComment = async (post, comment) => {
  if (!confirm("Delete this comment?")) return
  try {
    await deleteCommentApi(comment.id)
    post.comments = post.comments.filter(c => c.id !== comment.id)
    displayToast("Comment deleted ✅")
  } catch {
    displayToast("Failed to delete comment ❌")
  }
}

// -------------------- POSTS FETCH -------------------- //
const currentPage = ref(1)
const lastPage = ref(1)

const fetchPosts = async (page = 1) => {
  const token = localStorage.getItem("token")
  if (!token) return error.value = "No token found. Please login again."
  currentUser.value = JSON.parse(localStorage.getItem("user") || "{}")

  try {
    const res = await fetchPostsApi(page)
    currentPage.value = res.data.current_page
    lastPage.value = res.data.last_page

    posts.value = res.data.data.map(p => ({
      ...p,
      liked: p.likes.some(l => l.user_id === currentUser.value.id),
      like_id: p.likes.find(l => l.user_id === currentUser.value.id)?.id || null,
      likes_count: p.likes.length,
      like_users: p.likes.map(l => l.user),
      comments: p.comments.map(c => ({ id: c.id, text: c.comment_text, user: c.user })),
      showCommentBox: false,
      newComment: ""
    }))
  } catch {
    error.value = "Failed to load posts"
  }
}

// -------------------- LIFECYCLE -------------------- //
const handleClickOutside = (event) => {
  const dropdowns = document.querySelectorAll(".post-dropdown")
  if (![...dropdowns].some(el => el.contains(event.target))) openPostId.value = null
}

onMounted(() => {
  fetchPosts(currentPage.value)
  document.addEventListener("click", handleClickOutside)
})

onBeforeUnmount(() => document.removeEventListener("click", handleClickOutside))

const logout = () => { localStorage.removeItem('token'); router.push('/login') }
</script>

