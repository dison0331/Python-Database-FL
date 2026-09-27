<template>
  <div class="knowledge">
    <header class="page-header">
      <h1>Python 知识查询</h1>
      <div class="filters">
        <select v-model="selectedVersion" @change="filter" class="filter-select">
          <option value="">所有版本</option>
          <option v-for="version in versions" :key="version.version" :value="version.version">
            {{ version.name }}
          </option>
        </select>
        <select v-model="selectedCategory" @change="filter" class="filter-select">
          <option value="">所有分类</option>
          <option v-for="category in categories" :key="category.name" :value="category.name">
            {{ category.name }}
          </option>
        </select>
        <input
          v-model="searchKeyword"
          @keyup.enter="search"
          type="text"
          placeholder="搜索关键词..."
          class="search-input"
        />
        <button @click="search" class="search-button">搜索</button>
      </div>
    </header>

    <main class="main-content">
      <div class="knowledge-grid">
        <div
          v-for="item in knowledgeList"
          :key="item.id"
          class="knowledge-card"
          @click="goToDetail(item.id)"
        >
          <div class="card-header">
            <span class="version-badge">{{ item.version }}</span>
            <span class="category-badge">{{ item.category }}</span>
          </div>
          <h3 class="title">{{ item.title }}</h3>
          <p class="description">{{ item.content.substring(0, 100) }}...</p>
          <div class="tags">
            <span v-for="tag in item.tags" :key="tag" class="tag">{{ tag }}</span>
          </div>
          <div class="card-footer">
            <span class="author">{{ item.author || '系统' }}</span>
            <span class="date">{{ formatDate(item.created_at) }}</span>
          </div>
        </div>
      </div>

      <div v-if="knowledgeList.length === 0" class="empty-state">
        <p>未找到相关知识</p>
        <button @click="clearFilters" class="clear-filters">清除筛选条件</button>
      </div>

      <div v-if="totalPages > 1" class="pagination">
        <button
          @click="prevPage"
          :disabled="currentPage === 1"
          class="page-button"
        >
          上一页
        </button>
        <span class="page-info">第 {{ currentPage }} / {{ totalPages }} 页</span>
        <button
          @click="nextPage"
          :disabled="currentPage === totalPages"
          class="page-button"
        >
          下一页
        </button>
      </div>
    </main>
  </div>
</template>

<script>
import { ref, onMounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { knowledgeService } from '../services/knowledge'

export default {
  name: 'Knowledge',
  setup() {
    const router = useRouter()
    const route = useRoute()
    const knowledgeList = ref([])
    const versions = ref([])
    const categories = ref([])
    const selectedVersion = ref('')
    const selectedCategory = ref('')
    const searchKeyword = ref('')
    const currentPage = ref(1)
    const totalPages = ref(1)

    onMounted(async () => {
      await loadInitialData()
      applyRouteParams()
    })

    watch(() => route.query, async () => {
      applyRouteParams()
      await loadKnowledge()
    })

    const loadInitialData = async () => {
      try {
        const [versionsRes, categoriesRes] = await Promise.all([
          knowledgeService.getVersions(),
          knowledgeService.getCategories()
        ])
        versions.value = versionsRes.data
        categories.value = categoriesRes.data
      } catch (error) {
        console.error('加载初始数据失败:', error)
      }
    }

    const applyRouteParams = () => {
      selectedVersion.value = route.query.version || ''
      selectedCategory.value = route.query.category || ''
      searchKeyword.value = route.query.keyword || ''
      currentPage.value = parseInt(route.query.page) || 1
    }

    const loadKnowledge = async () => {
      try {
        const params = {
          version: selectedVersion.value,
          category: selectedCategory.value,
          keyword: searchKeyword.value,
          page: currentPage.value,
          per_page: 20
        }
        const res = await knowledgeService.getKnowledgeList(params)
        knowledgeList.value = res.data.data
        totalPages.value = res.data.last_page
      } catch (error) {
        console.error('加载知识列表失败:', error)
      }
    }

    const filter = () => {
      const query = {}
      if (selectedVersion.value) query.version = selectedVersion.value
      if (selectedCategory.value) query.category = selectedCategory.value
      if (currentPage.value > 1) query.page = currentPage.value
      router.push({ query })
    }

    const search = () => {
      currentPage.value = 1
      filter()
    }

    const prevPage = () => {
      if (currentPage.value > 1) {
        currentPage.value--
        filter()
      }
    }

    const nextPage = () => {
      if (currentPage.value < totalPages.value) {
        currentPage.value++
        filter()
      }
    }

    const clearFilters = () => {
      selectedVersion.value = ''
      selectedCategory.value = ''
      searchKeyword.value = ''
      currentPage.value = 1
      router.push({ query: {} })
    }

    const goToDetail = (id) => {
      router.push({ name: 'KnowledgeDetail', params: { id } })
    }

    const formatDate = (date) => {
      return new Date(date).toLocaleDateString('zh-CN')
    }

    return {
      knowledgeList,
      versions,
      categories,
      selectedVersion,
      selectedCategory,
      searchKeyword,
      currentPage,
      totalPages,
      filter,
      search,
      prevPage,
      nextPage,
      clearFilters,
      goToDetail,
      formatDate
    }
  }
}
</script>

<style scoped>
.page-header {
  background: #f8f9fa;
  padding: 32px 20px;
  margin-bottom: 32px;
}

.page-header h1 {
  font-size: 32px;
  margin-bottom: 24px;
  color: #3776ab;
}

.filters {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
  max-width: 800px;
}

.filter-select, .search-input {
  padding: 12px 16px;
  border: 1px solid #ddd;
  border-radius: 8px;
  font-size: 14px;
  outline: none;
}

.filter-select {
  min-width: 150px;
}

.search-input {
  flex: 1;
  min-width: 200px;
}

.search-button {
  padding: 12px 24px;
  background: #3776ab;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 14px;
}

.search-button:hover {
  background: #2b5c8a;
}

.main-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

.knowledge-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 20px;
}

.knowledge-card {
  background: white;
  border: 1px solid #e0e0e0;
  border-radius: 12px;
  padding: 24px;
  cursor: pointer;
  transition: all 0.3s;
}

.knowledge-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
}

.card-header {
  display: flex;
  gap: 8px;
  margin-bottom: 12px;
}

.version-badge, .category-badge {
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: bold;
}

.version-badge {
  background: #3776ab;
  color: white;
}

.category-badge {
  background: #ffd43b;
  color: #3776ab;
}

.title {
  font-size: 18px;
  margin-bottom: 8px;
  color: #333;
}

.description {
  font-size: 14px;
  color: #666;
  margin-bottom: 12px;
}

.tags {
  display: flex;
  gap: 8px;
  margin-bottom: 12px;
  flex-wrap: wrap;
}

.tag {
  background: #f0f0f0;
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 12px;
  color: #666;
}

.card-footer {
  display: flex;
  justify-content: space-between;
  font-size: 12px;
  color: #999;
}

.empty-state {
  text-align: center;
  padding: 60px 20px;
  color: #666;
}

.clear-filters {
  margin-top: 16px;
  padding: 12px 24px;
  background: #3776ab;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 24px;
  margin-top: 40px;
}

.page-button {
  padding: 12px 24px;
  background: white;
  border: 1px solid #ddd;
  border-radius: 8px;
  cursor: pointer;
}

.page-button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-info {
  font-size: 14px;
  color: #666;
}
</style>
