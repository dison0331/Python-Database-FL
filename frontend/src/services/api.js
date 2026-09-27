import axios from 'axios'

const API_BASE_URL = import.meta.env.VITE_API_URL || '/api/v1'

const api = axios.create({
  baseURL: API_BASE_URL,
  timeout: 10000,
  headers: {
    'Content-Type': 'application/json'
  }
})

// 请求拦截器
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// 响应拦截器
api.interceptors.response.use(
  (response) => {
    return response.data
  },
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      window.location.href = '/login'
    }
    return Promise.reject(error)
  }
)

export const authService = {
  register(data) {
    return api.post('/auth/register', data)
  },

  login(data) {
    return api.post('/auth/login', data)
  },

  logout() {
    return api.post('/auth/logout')
  },

  getUser() {
    return api.get('/auth/user')
  },

  updateProfile(data) {
    return api.put('/auth/user/profile', data)
  },

  updatePassword(data) {
    return api.put('/auth/user/password', data)
  },

  githubLogin() {
    return api.get('/auth/github')
  },

  githubCallback(code) {
    return api.get(`/auth/github/callback?code=${code}`)
  },

  linkGithub(code) {
    return api.post('/auth/github/link', { code })
  }
}

export const knowledgeService = {
  getVersions() {
    return api.get('/knowledge/versions')
  },

  getCategories() {
    return api.get('/knowledge/categories')
  },

  getTags() {
    return api.get('/knowledge/tags')
  },

  getKnowledgeList(params) {
    return api.get('/knowledge', { params })
  },

  getKnowledgeDetail(id) {
    return api.get(`/knowledge/${id}`)
  },

  getByVersion(version) {
    return api.get(`/knowledge/version/${version}`)
  },

  getByCategory(category) {
    return api.get(`/knowledge/category/${category}`)
  },

  search(keyword, params) {
    return api.post('/knowledge/search', { keyword, ...params })
  },

  favorite(knowledgeId, knowledgeBaseId) {
    return api.post(`/knowledge/${knowledgeId}/favorite`, { knowledge_base_id: knowledgeBaseId })
  },

  removeFavorite(knowledgeId) {
    return api.delete(`/knowledge/${knowledgeId}/favorite`)
  },

  getPopularKnowledge() {
    return api.get('/knowledge/popular')
  }
}

export const knowledgeBaseService = {
  getKnowledgeBases() {
    return api.get('/knowledge-base')
  },

  createKnowledgeBase(data) {
    return api.post('/knowledge-base', data)
  },

  getKnowledgeBase(id) {
    return api.get(`/knowledge-base/${id}`)
  },

  updateKnowledgeBase(id, data) {
    return api.put(`/knowledge-base/${id}`, data)
  },

  deleteKnowledgeBase(id) {
    return api.delete(`/knowledge-base/${id}`)
  },

  addEntry(knowledgeBaseId, data) {
    return api.post(`/knowledge-base/${knowledgeBaseId}/entries`, data)
  },

  updateEntry(knowledgeBaseId, entryId, data) {
    return api.put(`/knowledge-base/${knowledgeBaseId}/entries/${entryId}`, data)
  },

  removeEntry(knowledgeBaseId, entryId) {
    return api.delete(`/knowledge-base/${knowledgeBaseId}/entries/${entryId}`)
  },

  addCategory(knowledgeBaseId, data) {
    return api.post(`/knowledge-base/${knowledgeBaseId}/categories`, data)
  },

  removeCategory(knowledgeBaseId, categoryId) {
    return api.delete(`/knowledge-base/${knowledgeBaseId}/categories/${categoryId}`)
  },

  exportKnowledgeBase(id, format) {
    return api.get(`/knowledge-base/${id}/export?format=${format}`)
  }
}

export const shareService = {
  createShare(knowledgeBaseId) {
    return api.post(`/knowledge-base/${knowledgeBaseId}/share`)
  },

  cancelShare(knowledgeBaseId) {
    return api.delete(`/knowledge-base/${knowledgeBaseId}/share`)
  },

  getSharedKnowledgeBase(shareId) {
    return api.get(`/share/${shareId}`)
  },

  getSharedEntries(shareId) {
    return api.get(`/share/${shareId}/entries`)
  },

  favoriteShare(shareId) {
    return api.post(`/share/${shareId}/favorite`)
  }
}

export const adminService = {
  getUsers(params) {
    return api.get('/admin/users', { params })
  },

  updateUser(id, data) {
    return api.put(`/admin/users/${id}`, data)
  },

  deleteUser(id) {
    return api.delete(`/admin/users/${id}`)
  },

  disableUser(id) {
    return api.post(`/admin/users/${id}/disable`)
  },

  enableUser(id) {
    return api.post(`/admin/users/${id}/enable`)
  },

  getKnowledgeBases(params) {
    return api.get('/admin/knowledge-bases', { params })
  },

  deleteKnowledgeBase(id) {
    return api.delete(`/admin/knowledge-bases/${id}`)
  },

  approveKnowledgeBase(id) {
    return api.post(`/admin/knowledge-bases/${id}/approve`)
  },

  rejectKnowledgeBase(id) {
    return api.post(`/admin/knowledge-bases/${id}/reject`)
  },

  getStats() {
    return api.get('/admin/stats')
  },

  getNotifications(params) {
    return api.get('/admin/notifications', { params })
  },

  sendNotification(data) {
    return api.post('/admin/notifications', data)
  },

  getSettings() {
    return api.get('/admin/settings')
  },

  updateSettings(data) {
    return api.put('/admin/settings', data)
  },

  getLogs(params) {
    return api.get('/admin/logs', { params })
  },

  getAuditLogs(params) {
    return api.get('/admin/audit-logs', { params })
  },

  createBackup() {
    return api.post('/admin/backup')
  },

  getBackups() {
    return api.get('/admin/backups')
  },

  restoreBackup(id) {
    return api.post(`/admin/backups/${id}/restore`)
  }
}

export default api
