# Python 知识库网站

> 基于 Laravel 10 + Vue 3 的 Python 知识查询、个人知识库管理及分享平台

## 📋 项目概述

本项目是一个 Python 知识库网站，提供以下核心功能：

- **知识查询**: 查询 Python 3.8 ~ 3.12 各版本知识、常用库、框架、工具、代码片段等
- **个人知识库**: 用户可创建个人知识库，收藏、整理、导出知识内容
- **知识分享**: 用户可分享知识库给其他用户，支持分享 ID 访问
- **用户管理**: 支持邮箱注册、GitHub OAuth 登录、账号关联
- **管理员功能**: 内容审核、用户管理、系统监控、数据备份

## 🛠️ 技术栈

| 类别 | 技术 | 版本 |
|------|------|------|
| **后端语言** | PHP | 8.2+ |
| **后端框架** | Laravel | 10.x |
| **前端框架** | Vue | 3.4+ |
| **构建工具** | Vite | 5.x |
| **数据库** | MySQL | 5.7+ |
| **缓存** | Redis | 6.2+ |
| **认证** | Laravel Passport/Sanctum | OAuth2 |
| **部署** | 宝塔面板 | - |

## 📁 目录结构

```
python-knowledge-base/
├── app/                    # 应用核心代码
│   ├── Http/Controllers/   # 控制器
│   └── Models/             # 数据模型
├── database/               # 数据库相关
│   ├── migrations/         # 迁移脚本
│   └── seeders/            # 种子数据
├── routes/                 # 路由配置
│   ├── api.php             # API 路由
│   └── web.php             # Web 路由
├── frontend/               # 前端代码
│   ├── src/
│   │   ├── views/          # 页面组件
│   │   ├── router/         # 路由配置
│   │   └── services/       # API 服务
│   └── vite.config.js      # Vite 配置
├── tktk/                   # 技术文档目录
│   ├── 技术交接文档.md
│   ├── 部署与运维手册.md
│   ├── API接口文档.md
│   └── 数据库设计文档.md
├── composer.json           # PHP 依赖
├── package.json            # 前端依赖
├── .env.example            # 环境变量模板
├── deploy.sh               # 部署脚本
└── README.md               # 项目说明
```

## 🚀 快速开始

### 环境要求

- PHP 8.2+
- MySQL 5.7+
- Redis 6.2+
- Node.js 18+
- Composer 2.5+

### 安装步骤

```bash
# 1. 克隆项目
git clone <repository-url>
cd python-knowledge-base

# 2. 安装 PHP 依赖
composer install --no-dev --optimize-autoloader

# 3. 安装前端依赖
cd frontend
npm install --production
npm run build

# 4. 配置环境变量
cd ..
cp .env.example .env
php artisan key:generate

# 5. 配置数据库
# 编辑 .env 文件，设置数据库连接信息

# 6. 执行数据库迁移
php artisan migrate --force

# 7. 填充种子数据
php artisan db:seed

# 8. 设置权限
chmod -R 775 storage bootstrap/cache
```

### 宝塔面板部署

详见 `tktk/部署与运维手册.md`

## 📖 文档

| 文档 | 说明 |
|------|------|
| [技术交接文档](tktk/技术交接文档.md) | 项目架构、技术栈、部署指南 |
| [部署与运维手册](tktk/部署与运维手册.md) | 宝塔部署、日常运维、故障排查 |
| [API 接口文档](tktk/API接口文档.md) | 接口说明、请求响应示例 |
| [数据库设计文档](tktk/数据库设计文档.md) | 表结构、ER 图、索引优化 |

## 🔌 API 接口

### 认证接口

| 方法 | 路径 | 说明 |
|------|------|------|
| POST | `/api/v1/auth/register` | 用户注册 |
| POST | `/api/v1/auth/login` | 用户登录 |
| GET | `/api/v1/auth/github` | GitHub 登录 |

### 知识查询接口

| 方法 | 路径 | 说明 |
|------|------|------|
| GET | `/api/v1/knowledge/versions` | 获取版本列表 |
| GET | `/api/v1/knowledge` | 获取知识列表 |
| GET | `/api/v1/knowledge/{id}` | 获取知识详情 |

### 知识库接口

| 方法 | 路径 | 说明 |
|------|------|------|
| GET | `/api/v1/knowledge-base` | 获取知识库列表 |
| POST | `/api/v1/knowledge-base` | 创建知识库 |
| GET | `/api/v1/knowledge-base/{id}/export` | 导出知识库 |

## 🔐 安全特性

- bcrypt 密码加密
- HTTPS 强制传输
- CSRF 防护
- XSS 过滤
- 敏感词检测
- 审计日志

## 📊 性能指标

| 指标 | 目标值 |
|------|--------|
| 知识查询响应时间 | ≤ 500ms (P95) |
| 首屏加载时间 | ≤ 2s (P95) |
| 并发用户数 | ≥ 1000 |
| 系统可用性 | ≥ 99.5% |

## 📞 联系方式

**项目负责人**: [dison0331](https://github.com/dison0331)  
**技术支持**: [dison0331](https://github.com/dison0331)  (GitHub)
**文档版本**: 1.0.0  
**最后更新**: 2026-09-27

---

## 📝 变更记录

| 版本 | 日期 | 变更内容 |
|------|------|----------|
| 1.0.0 | 2026-09-27 | 初始版本，实现核心功能 |

---

**许可证**: N/A
