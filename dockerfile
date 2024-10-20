# 使用官方 PHP 镜像
FROM php:8.1-cli

# 更新软件包列表并安装依赖
RUN apt-get update && apt-get install -y \
    libevent-dev \
    libssl-dev \
    git \
    unzip \
    iputils-ping \
    && docker-php-ext-install pcntl posix sockets pdo_sqlite

# 下载并安装 event 扩展
RUN curl -o event-3.1.2.tgz http://pecl.php.net/get/event-3.1.2.tgz \
    && pecl install event-3.1.2.tgz

# 添加 event 配置文件
RUN cd /usr/local/etc/php/conf.d \
    && touch event.ini \
    && echo '[event]' > event.ini \
    && echo 'extension=event.so' >> event.ini

# 清理 APT 缓存和临时文件
RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# 设置工作目录
WORKDIR /var/www

# 暴露 Workerman 监听的默认端口（可根据实际情况调整）
EXPOSE 80

# 将 Workerman 的入口脚本作为容器的默认执行命令
CMD ["php", "B_backend.php", "start"]
