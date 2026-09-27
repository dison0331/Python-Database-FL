<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KnowledgeSeeder extends Seeder
{
    public function run()
    {
        $this->seedVersions();
        $this->seedCategories();
        $this->seedKnowledge();
    }

    protected function seedVersions()
    {
        $versions = [
            ['version' => '3.8', 'name' => 'Python 3.8', 'release_date' => '2019-10-14', 'is_active' => true],
            ['version' => '3.9', 'name' => 'Python 3.9', 'release_date' => '2020-10-05', 'is_active' => true],
            ['version' => '3.10', 'name' => 'Python 3.10', 'release_date' => '2021-10-04', 'is_active' => true],
            ['version' => '3.11', 'name' => 'Python 3.11', 'release_date' => '2022-10-24', 'is_active' => true],
            ['version' => '3.12', 'name' => 'Python 3.12', 'release_date' => '2023-10-02', 'is_active' => true],
        ];
        DB::table('versions')->insert($versions);
    }

    protected function seedCategories()
    {
        $categories = [
            ['name' => '常用库', 'description' => 'Python 常用第三方库', 'order' => 1],
            ['name' => '常用框架', 'description' => 'Python Web 框架', 'order' => 2],
            ['name' => '常用工具', 'description' => 'Python 开发工具', 'order' => 3],
            ['name' => '代码片段', 'description' => '常用代码示例', 'order' => 4],
            ['name' => '配置文件', 'description' => '常用配置文件模板', 'order' => 5],
            ['name' => '环境变量', 'description' => '环境配置相关', 'order' => 6],
            ['name' => '常用命令', 'description' => '命令行工具使用', 'order' => 7],
            ['name' => '函数知识', 'description' => '内置函数详解', 'order' => 8],
        ];
        DB::table('categories')->insert($categories);
    }

    protected function seedKnowledge()
    {
        $knowledge = [
            [
                'title' => 'Python 3.8 海象运算符',
                'content' => 'Python 3.8 引入了海象运算符 :=，允许在表达式中赋值。例如：if (data := get_data()) is not None: 可以简化代码。',
                'version' => '3.8',
                'category' => '新特性',
                'tags' => '["海象运算符", "赋值表达式", "新特性"]',
                'is_system' => true,
                'status' => 'published'
            ],
            [
                'title' => 'Python 3.9 字典合并运算符',
                'content' => 'Python 3.9 引入了 | 和 |= 运算符用于字典合并。例如：dict1 | dict2 返回合并后的字典。',
                'version' => '3.9',
                'category' => '新特性',
                'tags' => '["字典", "合并", "新特性"]',
                'is_system' => true,
                'status' => 'published'
            ],
            [
                'title' => 'Python 3.10 模式匹配',
                'content' => 'Python 3.10 引入了 match-case 语句，类似于其他语言中的 switch-case，但功能更强大。',
                'version' => '3.10',
                'category' => '新特性',
                'tags' => '["模式匹配", "match-case", "新特性"]',
                'is_system' => true,
                'status' => 'published'
            ],
            [
                'title' => 'NumPy 数组基础',
                'content' => 'NumPy 是 Python 科学计算的基础库，提供多维数组对象和各种派生对象。',
                'version' => '3.8',
                'category' => '常用库',
                'tags' => '["NumPy", "数组", "科学计算"]',
                'is_system' => true,
                'status' => 'published'
            ],
            [
                'title' => 'Pandas DataFrame 基础',
                'content' => 'Pandas 是 Python 数据分析的核心库，DataFrame 是其主要数据结构，提供灵活的数据处理能力。',
                'version' => '3.8',
                'category' => '常用库',
                'tags' => '["Pandas", "DataFrame", "数据分析"]',
                'is_system' => true,
                'status' => 'published'
            ],
            [
                'title' => 'Requests HTTP 请求',
                'content' => 'Requests 是 Python 最流行的 HTTP 库，提供简洁的 API 用于发送 HTTP 请求。',
                'version' => '3.8',
                'category' => '常用库',
                'tags' => '["Requests", "HTTP", "网络请求"]',
                'is_system' => true,
                'status' => 'published'
            ],
            [
                'title' => 'Django 框架入门',
                'content' => 'Django 是 Python 最流行的 Web 框架，提供完整的 MVC 架构和 ORM 支持。',
                'version' => '3.8',
                'category' => '常用框架',
                'tags' => '["Django", "Web框架", "ORM"]',
                'is_system' => true,
                'status' => 'published'
            ],
            [
                'title' => 'Flask 框架入门',
                'content' => 'Flask 是轻量级 Web 框架，适合快速开发和微服务架构。',
                'version' => '3.8',
                'category' => '常用框架',
                'tags' => '["Flask", "Web框架", "轻量级"]',
                'is_system' => true,
                'status' => 'published'
            ],
            [
                'title' => 'FastAPI 框架入门',
                'content' => 'FastAPI 是现代 Python Web 框架，支持异步编程，自动生成 API 文档。',
                'version' => '3.8',
                'category' => '常用框架',
                'tags' => '["FastAPI", "Web框架", "异步"]',
                'is_system' => true,
                'status' => 'published'
            ],
            [
                'title' => 'requirements.txt 配置',
                'content' => 'requirements.txt 用于记录项目依赖，使用 pip install -r requirements.txt 安装。',
                'version' => '3.8',
                'category' => '配置文件',
                'tags' => '["requirements", "依赖", "配置"]',
                'is_system' => true,
                'status' => 'published'
            ],
            [
                'title' => 'pyproject.toml 配置',
                'content' => 'pyproject.toml 是现代 Python 项目的配置文件，支持 PEP 621 标准。',
                'version' => '3.8',
                'category' => '配置文件',
                'tags' => '["pyproject", "配置", "PEP621"]',
                'is_system' => true,
                'status' => 'published'
            ],
            [
                'title' => '虚拟环境创建',
                'content' => '使用 python -m venv myenv 创建虚拟环境，激活后使用 pip 安装依赖。',
                'version' => '3.8',
                'category' => '环境变量',
                'tags' => '["虚拟环境", "venv", "环境配置"]',
                'is_system' => true,
                'status' => 'published'
            ],
            [
                'title' => 'pip 常用命令',
                'content' => 'pip install 安装包，pip list 列出已安装包，pip freeze 生成依赖列表。',
                'version' => '3.8',
                'category' => '常用命令',
                'tags' => '["pip", "命令", "包管理"]',
                'is_system' => true,
                'status' => 'published'
            ],
            [
                'title' => 'len() 函数',
                'content' => 'len() 函数返回对象的长度，适用于字符串、列表、字典等可迭代对象。',
                'version' => '3.8',
                'category' => '函数知识',
                'tags' => '["len", "内置函数", "长度"]',
                'is_system' => true,
                'status' => 'published'
            ],
            [
                'title' => 'map() 函数',
                'content' => 'map() 函数将函数应用于可迭代对象的每个元素，返回迭代器。',
                'version' => '3.8',
                'category' => '函数知识',
                'tags' => '["map", "内置函数", "映射"]',
                'is_system' => true,
                'status' => 'published'
            ],
        ];
        DB::table('knowledge')->insert($knowledge);
    }
}
