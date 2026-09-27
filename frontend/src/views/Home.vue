<template>
  <div class="home">
    <header class="header">
      <div class="header-content">
        <h1 class="logo">Python 知识库</h1>
        <p class="subtitle">Python 知识查询、个人知识库管理及分享服务</p>
        <div class="search-box">
          <input
            v-model="searchKeyword"
            @keyup.enter="search"
            type="text"
            placeholder="搜索 Python 知识..."
            class="search-input"
          />
          <button @click="search" class="search-button">搜索</button>
        </div>
        <div class="quick-links">
          <router-link to="/knowledge?category=常用库" class="quick-link">常用库</router-link>
          <router-link to="/knowledge?category=常用框架" class="quick-link">常用框架</router-link>
          <router-link to="/knowledge?category=代码片段" class="quick-link">代码片段</router-link>
          <router-link to="/knowledge?category=配置文件" class="quick-link">配置文件</router-link>
        </div>
      </div>
    </header>

    <main class="main">
      <section class="versions">
        <h2>Python 版本</h2>
        <div class="version-list">
          <div
            v-for="version in versions"
            :key="version.version"
            class="version-card"
            @click="selectVersion(version.version)"
          >
            <h3>{{ version.name }}</h3>
            <p>发布日期: {{ version.release_date }}</p>
          </div>
        </div>
      </section>

      <section class="categories">
        <h2>知识分类</h2>
        <div class="category-list">
          <div
            v-for="category in categories"
            :key="category.name"
            class="category-card"
            @click="selectCategory(category.name)"
          >
            <h3>{{ category.name }}</h3>
            <p>{{ category.description }}</p>
          </div>
        </div>
      </section>

      <section class="popular">
        <h2>热门知识</h2>
        <div class="knowledge-list">
          <div
            v-for="item in popularKnowledge"
            :key="item.id"
            class="knowledge-card"
            @click="goToDetail(item.id)"
          >
            <h3>{{ item.title }}</h3>
            <p class="version">版本: {{ item.version }}</p>
            <p class="category">分类: {{ item.category }}</p>
            <div class="tags">
              <span v-for="tag in item.tags" :key="tag" class="tag">{{ tag }}</span>
            </div>
          </div>
        </div>
      </section>
    </main>

    <footer class="footer">
      <p>© 2024 Python 知识库. 保留所有权利.</p>
    </footer>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { knowledgeService } from '../services/knowledge'

export default {
  name: 'Home',
  setup() {
    const router = useRouter()
    const searchKeyword = ref('')
    const versions = ref([])
    const categories = ref([])
    const popularKnowledge = ref([])

    onMounted(async () => {
      await loadInitialData()
    })

    const loadInitialData = async () => {
      try {
        const [versionsRes, categoriesRes, popularRes] = await Promise.all([
          knowledgeService.getVersions(),
          knowledgeService.getCategories(),
          knowledgeService.getPopularKnowledge()
        ])
        versions.value = versionsRes.data
        categories.value = categoriesRes.data
        popularKnowledge.value = popularRes.data
      } catch (error) {
        console.error('加载初始数据失败:', error)
      }
    }

    const search = () => {
      if (searchKeyword.value.trim()) {
        router.push({ name: 'Knowledge', query: { keyword: searchKeyword.value } })
      }
    }

    const selectVersion = (version) => {
      router.push({ name: 'Knowledge', query: { version } })
    }

    const selectCategory = (category) => {
      router.push({ name: 'Knowledge', query: { category } })
    }

    const goToDetail = (id) => {
      router.push({ name: 'KnowledgeDetail', params: { id } })
    }

    return {
      searchKeyword,
      versions,
      categories,
      popularKnowledge,
      search,
      selectVersion,
      selectCategory,
      goToDetail
    }
  }
}
</script>

<style scoped>
.header {
  background: linear-gradient(135deg, #3776ab 0%, #2b5c8a 100%);
  color: white;
  padding: 60px 20px;
  text-align: center;
}

.header-content {
  max-width: 800px;
  margin: 0 auto;
}

.logo {
  font-size: 48px;
  margin-bottom: 16px;
}

.subtitle {
  font-size: 18px;
  opacity: 0.9;
  margin-bottom: 32px;
}

.search-box {
  display: flex;
  gap: 12px;
  max-width: 600px;
  margin: 0 auto 32px;
}

.search-input {
  flex: 1;
  padding: 16px 20px;
  font-size: 16px;
  border: none;
  border-radius: 8px;
  outline: none;
}

.search-button {
  padding: 16px 32px;
  font-size: 16px;
  background: #ffd43b;
  color: #3776ab;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-weight: bold;
}

.search-button:hover {
  background: #ffc209;
}

.quick-links {
  display: flex;
  gap: 16px;
  justify-content: center;
  flex-wrap: wrap;
}

.quick-link {
  color: white;
  text-decoration: none;
  padding: 8px 16px;
  border: 1px solid rgba(255, 255, 255, 0.3);
  border-radius: 20px;
  transition: all 0.3s;
}

.quick-link:hover {
  background: rgba(255, 255, 255, 0.2);
}

.main {
  max-width: 1200px;
  margin: 0 auto;
  padding: 40px 20px;
}

.versions, .categories, .popular {
  margin-bottom: 60px;
}

h2 {
  font-size: 28px;
  margin-bottom: 24px;
  color: #3776ab;
}

.version-list, .category-list, .knowledge-list {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 20px;
}

.version-card, .category-card, .knowledge-card {
  background: white;
  border: 1px solid #e0e0e0;
  border-radius: 12px;
  padding: 24px;
  cursor: pointer;
  transition: all 0.3s;
}

.version-card:hover, .category-card:hover, .knowledge-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
}

.version-card h3, .category-card h3, .knowledge-card h3 {
  font-size: 20px;
  margin-bottom: 8px;
  color: #3776ab;
}

.version-card p, .category-card p {
  color: #666;
  font-size: 14px;
}

.knowledge-card .version, .knowledge-card .category {
  font-size: 14px;
  color: #666;
  margin: 4px 0;
}

.tags {
  display: flex;
  gap: 8px;
  margin-top: 12px;
  flex-wrap: wrap;
}

.tag {
  background: #f0f0f0;
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 12px;
  color: #666;
}

.footer {
  text-align: center;
  padding: 40px 20px;
  background: #f5f5f5;
  color: #666;
}
</style>
