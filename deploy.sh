#!/bin/bash

# ============================================
# Python 知识库 - 宝塔面板部署脚本
# ============================================
# 作者: CodeArts
# 版本: 1.0.0
# 日期: 2024-01-01
# ============================================

set -e

# 颜色定义
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# 项目配置
PROJECT_NAME="python-knowledge-base"
PROJECT_DIR="/www/wwwroot/${PROJECT_NAME}"
GIT_REPO=""
PHP_VERSION="8.2"
MYSQL_VERSION="5.7"
REDIS_VERSION="6.2"

# 日志函数
log_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

log_warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

log_header() {
    echo -e "${BLUE}========================================${NC}"
    echo -e "${BLUE} $1${NC}"
    echo -e "${BLUE}========================================${NC}"
}

# 检查命令是否存在
check_command() {
    if ! command -v $1 &> /dev/null; then
        log_error "命令 $1 未找到，请先安装"
        exit 1
    fi
}

# 显示帮助信息
show_help() {
    cat << EOF
用法: $0 [命令]

命令:
    install       一键安装（部署完整项目）
    update        更新代码
    restart       重启服务
    backup        备份数据
    restore       恢复数据
    status        查看状态
    help          显示帮助信息

环境要求:
    - PHP ${PHP_VERSION}+
    - MySQL ${MYSQL_VERSION}+
    - Redis ${REDIS_VERSION}+
    - Composer
    - Node.js 18+

示例:
    $0 install
    $0 update
    $0 restart
EOF
}

# 检查环境
check_environment() {
    log_header "检查环境"

    # 检查 PHP
    if ! command -v php &> /dev/null; then
        log_error "PHP 未安装，请先在宝塔面板安装 PHP ${PHP_VERSION}"
        exit 1
    fi
    PHP_VERSION_CURRENT=$(php -r "echo PHP_VERSION;")
    log_info "PHP 版本: ${PHP_VERSION_CURRENT}"

    # 检查 Composer
    if ! command -v composer &> /dev/null; then
        log_error "Composer 未安装，请先安装 Composer"
        exit 1
    fi
    log_info "Composer 版本: $(composer --version | head -1)"

    # 检查 Node.js
    if ! command -v node &> /dev/null; then
        log_error "Node.js 未安装，请先安装 Node.js 18+"
        exit 1
    fi
    log_info "Node.js 版本: $(node -v)"

    # 检查 MySQL
    if ! command -v mysql &> /dev/null; then
        log_warn "MySQL 未安装，请在宝塔面板安装 MySQL ${MYSQL_VERSION}"
    fi

    # 检查 Redis
    if ! command -v redis-cli &> /dev/null; then
        log_warn "Redis 未安装，请在宝塔面板安装 Redis ${REDIS_VERSION}"
    fi

    log_info "环境检查完成"
}

# 创建项目目录
create_project_dir() {
    log_header "创建项目目录"

    if [ -d "${PROJECT_DIR}" ]; then
        log_warn "项目目录已存在: ${PROJECT_DIR}"
        read -p "是否删除现有目录？(y/n): " confirm
        if [ "$confirm" = "y" ] || [ "$confirm" = "Y" ]; then
            rm -rf "${PROJECT_DIR}"
            log_info "已删除现有目录"
        else
            log_info "使用现有目录"
        fi
    fi

    mkdir -p "${PROJECT_DIR}"
    log_info "项目目录: ${PROJECT_DIR}"
}

# 下载代码
clone_repository() {
    log_header "下载代码"

    if [ -z "${GIT_REPO}" ]; then
        log_warn "未配置 GIT_REPO，请手动上传代码或配置仓库地址"
        read -p "请输入 Git 仓库地址: " GIT_REPO
    fi

    if [ -n "${GIT_REPO}" ]; then
        cd "${PROJECT_DIR}"
        git clone "${GIT_REPO}" .
        log_info "代码下载完成"
    else
        log_info "请将代码上传到 ${PROJECT_DIR}"
    fi
}

# 安装 PHP 依赖
install_php_dependencies() {
    log_header "安装 PHP 依赖"

    cd "${PROJECT_DIR}"

    # 配置 Composer 中国镜像
    composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/

    # 安装依赖
    composer install --no-dev --optimize-autoloader

    log_info "PHP 依赖安装完成"
}

# 安装前端依赖
install_frontend_dependencies() {
    log_header "安装前端依赖"

    cd "${PROJECT_DIR}"

    # 配置 npm 淘宝镜像
    npm config set registry https://registry.npmmirror.com/

    # 安装依赖
    npm install --production

    # 构建前端
    npm run build

    log_info "前端依赖安装完成"
}

# 配置环境变量
configure_environment() {
    log_header "配置环境变量"

    cd "${PROJECT_DIR}"

    # 复制 .env 文件
    if [ -f ".env.example" ]; then
        cp .env.example .env
        log_info "已创建 .env 文件"
    fi

    # 生成应用密钥
    php artisan key:generate
    log_info "已生成应用密钥"

    # 设置权限
    chmod -R 775 storage bootstrap/cache
    log_info "已设置目录权限"
}

# 配置数据库
configure_database() {
    log_header "配置数据库"

    cd "${PROJECT_DIR}"

    read -p "请输入数据库名称: " DB_NAME
    read -p "请输入数据库用户名: " DB_USER
    read -s -p "请输入数据库密码: " DB_PASSWORD
    echo

    # 更新 .env 文件
    sed -i "s/DB_DATABASE=python_knowledge/DB_DATABASE=${DB_NAME}/" .env
    sed -i "s/DB_USERNAME=your_username/DB_USERNAME=${DB_USER}/" .env
    sed -i "s/DB_PASSWORD=your_password/DB_PASSWORD=${DB_PASSWORD}/" .env

    log_info "数据库配置已更新"

    # 运行迁移
    read -p "是否运行数据库迁移？(y/n): " confirm
    if [ "$confirm" = "y" ] || [ "$confirm" = "Y" ]; then
        php artisan migrate --force
        log_info "数据库迁移完成"
    fi

    # 填充种子数据
    read -p "是否填充种子数据？(y/n): " confirm
    if [ "$confirm" = "y" ] || [ "$confirm" = "Y" ]; then
        php artisan db:seed
        log_info "种子数据填充完成"
    fi
}

# 配置宝塔面板
configure_baota() {
    log_header "配置宝塔面板"

    # 创建网站
    read -p "请输入网站域名: " DOMAIN
    if [ -n "${DOMAIN}" ]; then
        log_info "请在宝塔面板中手动创建网站，域名: ${DOMAIN}"
        log_info "网站根目录: ${PROJECT_DIR}/public"
    fi

    # 配置 PHP 版本
    log_info "请在宝塔面板中设置 PHP 版本: ${PHP_VERSION}"

    # 配置伪静态
    log_info "请在宝塔面板中配置伪静态规则:"
    echo ""
    echo "location / {"
    echo "    try_files \$uri \$uri/ /index.php?\$query_string;"
    echo "}"
    echo ""

    # 配置 SSL
    read -p "是否配置 SSL？(y/n): " confirm
    if [ "$confirm" = "y" ] || [ "$confirm" = "Y" ]; then
        log_info "请在宝塔面板中申请并配置 SSL 证书"
    fi

    # 配置 Redis
    log_info "请在宝塔面板中安装并配置 Redis"

    # 配置定时任务
    log_info "请在宝塔面板中添加以下定时任务:"
    echo ""
    echo "* * * * * cd ${PROJECT_DIR} && php artisan schedule:run >> /dev/null 2>&1"
    echo ""
}

# 配置队列
configure_queue() {
    log_header "配置队列"

    read -p "是否配置队列？(y/n): " confirm
    if [ "$confirm" = "y" ] || [ "$confirm" = "Y" ]; then
        log_info "请在宝塔面板中添加以下守护进程:"
        echo ""
        echo "php ${PROJECT_DIR}/artisan queue:work --sleep=3 --tries=3"
        echo ""
    fi
}

# 配置监控
configure_monitoring() {
    log_header "配置监控"

    log_info "请在宝塔面板中配置以下监控:"
    echo ""
    echo "1. 网站监控: 访问量、请求数、流量"
    echo "2. CPU 监控: 使用率、负载"
    echo "3. 内存监控: 使用率、可用内存"
    echo "4. 磁盘监控: 使用率、可用空间"
    echo ""
}

# 重启服务
restart_services() {
    log_header "重启服务"

    log_info "请在宝塔面板中重启以下服务:"
    echo ""
    echo "1. PHP-FPM"
    echo "2. Nginx/Apache"
    echo "3. MySQL"
    echo "4. Redis"
    echo ""

    log_info "服务重启完成"
}

# 显示完成信息
show_completion() {
    log_header "安装完成"

    cat << EOF

${GREEN}✓ Python 知识库安装完成！${NC}

访问地址: http://${DOMAIN}
后台地址: http://${DOMAIN}/admin

默认管理员账号:
  邮箱: admin@example.com
  密码: admin

请立即修改默认密码！

后续操作:
  1. 修改 .env 文件中的配置
  2. 配置邮件服务
  3. 配置 GitHub OAuth
  4. 配置监控告警

文档地址: https://docs.python-knowledge-base.com

EOF
}

# 主安装函数
install() {
    log_header "开始安装 Python 知识库"

    check_environment
    create_project_dir
    clone_repository
    install_php_dependencies
    install_frontend_dependencies
    configure_environment
    configure_database
    configure_baota
    configure_queue
    configure_monitoring
    restart_services
    show_completion
}

# 更新代码
update() {
    log_header "更新代码"

    cd "${PROJECT_DIR}"

    git pull
    composer install --no-dev --optimize-autoloader
    npm install --production
    npm run build
    php artisan migrate --force
    php artisan config:clear
    php artisan cache:clear

    log_info "代码更新完成"
}

# 重启服务
restart() {
    log_header "重启服务"

    log_info "请在宝塔面板中重启以下服务:"
    echo "1. PHP-FPM"
    echo "2. Nginx/Apache"
    echo "3. MySQL"
    echo "4. Redis"

    log_info "服务重启完成"
}

# 备份数据
backup() {
    log_header "备份数据"

    BACKUP_DIR="/www/backup/${PROJECT_NAME}"
    mkdir -p "${BACKUP_DIR}"

    # 备份数据库
    read -p "请输入数据库名称: " DB_NAME
    mysqldump -u root "${DB_NAME}" > "${BACKUP_DIR}/database_$(date +%Y%m%d_%H%M%S).sql"

    # 备份代码
    tar -czf "${BACKUP_DIR}/code_$(date +%Y%m%d_%H%M%S).tar.gz" "${PROJECT_DIR}"

    log_info "备份完成: ${BACKUP_DIR}"
}

# 恢复数据
restore() {
    log_header "恢复数据"

    log_info "请从备份目录选择备份文件: /www/backup/${PROJECT_NAME}"
    ls -la "/www/backup/${PROJECT_NAME}"

    read -p "请输入备份文件路径: " BACKUP_FILE

    if [ -f "${BACKUP_FILE}" ]; then
        tar -xzf "${BACKUP_FILE}" -C "${PROJECT_DIR}"
        log_info "恢复完成"
    else
        log_error "备份文件不存在"
    fi
}

# 查看状态
status() {
    log_header "查看状态"

    echo ""
    echo "项目目录: ${PROJECT_DIR}"
    echo "PHP 版本: $(php -r "echo PHP_VERSION;")"
    echo "Composer 版本: $(composer --version | head -1)"
    echo "Node.js 版本: $(node -v)"
    echo ""

    # 检查服务状态
    echo "服务状态:"
    systemctl status php-fpm-${PHP_VERSION} 2>/dev/null | grep "Active" || echo "PHP-FPM: 未知"
    systemctl status mysql 2>/dev/null | grep "Active" || echo "MySQL: 未知"
    systemctl status redis 2>/dev/null | grep "Active" || echo "Redis: 未知"
    echo ""
}

# 主函数
main() {
    case "$1" in
        install)
            install
            ;;
        update)
            update
            ;;
        restart)
            restart
            ;;
        backup)
            backup
            ;;
        restore)
            restore
            ;;
        status)
            status
            ;;
        help|--help|-h)
            show_help
            ;;
        *)
            show_help
            ;;
    esac
}

# 执行主函数
main "$@"
