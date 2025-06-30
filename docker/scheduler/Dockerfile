FROM php:8.3-cli

# Install minimum requirements
RUN apt-get update && apt-get install -y \
    cron \
    redis-tools \
    && rm -rf /var/lib/apt/lists/*

# Install only required PHP extensions
RUN docker-php-ext-install pdo pdo_mysql \
    && pecl install redis \
    && docker-php-ext-enable redis

# Configure cron
RUN mkdir -p /etc/cron.d \
    && touch /var/log/cron.log

COPY docker/scheduler/crontab /etc/cron.d/symfony
RUN chmod 0644 /etc/cron.d/symfony

WORKDIR /var/www

CMD ["cron", "-f"]