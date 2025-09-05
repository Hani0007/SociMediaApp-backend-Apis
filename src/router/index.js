import { createRouter, createWebHistory } from "vue-router";

// Import pages from src/pages
import RegisterPage from "@/pages/RegisterPage.vue";
import LoginPage from "@/pages/LoginPage.vue";
import MainFeedPage from "@/pages/MainFeedPage.vue";

const routes = [
  {
    path: "/",
    name: "Register",
    component: RegisterPage,
  },
  {
    path: "/login",
    name: "Login",
    component: LoginPage,
  },
  {
    path: "/feed",
    name: "Feed",
    component: MainFeedPage,
    meta: { requiresAuth: true }, // ✅ this route requires login
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
})
// ✅ Now add the guard AFTER router is created
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')

  if (to.meta.requiresAuth && !token) {
    // 🚫 Not logged in → redirect to login
    next({ name: 'Login' })
  } else {
    next() // ✅ Allow navigation
  }
})

export default router;
