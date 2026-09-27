import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Home.vue'
import Knowledge from '../views/Knowledge.vue'
import KnowledgeDetail from '../views/KnowledgeDetail.vue'
import KnowledgeBase from '../views/KnowledgeBase.vue'
import KnowledgeBaseDetail from '../views/KnowledgeBaseDetail.vue'
import Profile from '../views/Profile.vue'
import Share from '../views/Share.vue'
import Admin from '../views/Admin.vue'
import AdminUsers from '../views/AdminUsers.vue'
import AdminKnowledgeBases from '../views/AdminKnowledgeBases.vue'
import AdminSettings from '../views/AdminSettings.vue'
import AdminLogs from '../views/AdminLogs.vue'
import Login from '../views/Login.vue'
import Register from '../views/Register.vue'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home
  },
  {
    path: '/knowledge',
    name: 'Knowledge',
    component: Knowledge
  },
  {
    path: '/knowledge/:id',
    name: 'KnowledgeDetail',
    component: KnowledgeDetail,
    props: true
  },
  {
    path: '/knowledge-base',
    name: 'KnowledgeBase',
    component: KnowledgeBase,
    meta: { requiresAuth: true }
  },
  {
    path: '/knowledge-base/:id',
    name: 'KnowledgeBaseDetail',
    component: KnowledgeBaseDetail,
    props: true,
    meta: { requiresAuth: true }
  },
  {
    path: '/profile',
    name: 'Profile',
    component: Profile,
    meta: { requiresAuth: true }
  },
  {
    path: '/share/:shareId',
    name: 'Share',
    component: Share,
    props: true
  },
  {
    path: '/admin',
    name: 'Admin',
    component: Admin,
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/admin/users',
    name: 'AdminUsers',
    component: AdminUsers,
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/admin/knowledge-bases',
    name: 'AdminKnowledgeBases',
    component: AdminKnowledgeBases,
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/admin/settings',
    name: 'AdminSettings',
    component: AdminSettings,
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/admin/logs',
    name: 'AdminLogs',
    component: AdminLogs,
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { requiresGuest: true }
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
    meta: { requiresGuest: true }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    }
    if (to.hash) {
      return { el: to.hash, behavior: 'smooth' }
    }
    return { top: 0, behavior: 'smooth' }
  }
})

router.beforeEach((to, from, next) => {
  const requiresAuth = to.matched.some(record => record.meta.requiresAuth)
  const requiresGuest = to.matched.some(record => record.meta.requiresGuest)
  const requiresAdmin = to.matched.some(record => record.meta.requiresAdmin)
  const token = localStorage.getItem('token')
  const user = JSON.parse(localStorage.getItem('user') || 'null')

  if (requiresAuth && !token) {
    next({ name: 'Login', query: { redirect: to.fullPath } })
  } else if (requiresGuest && token) {
    next({ name: 'Home' })
  } else if (requiresAdmin && (!user || !user.is_admin)) {
    next({ name: 'Home' })
  } else {
    next()
  }
})

export default router
