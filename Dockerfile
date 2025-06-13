# =================================================================
# DOCKERFILE PER LARAVEL 12 + VUE.JS 3.5 + INERTIA.JS + TAILWINDCSS 4.0
# Ottimizzato per Linux Mint 22.1 (Ubuntu 24.04 LTS base)
# Multi-stage build per separare ambiente sviluppo e produzione
# =================================================================

# =========================================
# STAGE BASE: Configurazione comune
# =========================================
FROM php:8.4-fpm-bookworm AS base

# Informazioni del maintainer
LABEL maintainer="Laravel Developer <dev@example.com>"
LABEL description="Laravel 12 + Vue.js 3.5 + Inertia.js + TailwindCSS 4.0 Development Environment"
LABEL version="1.2"

# Variabili di build per gestione permessi Linux Mint 22.1
ARG UID=1000
ARG GID=1000
ARG USER=laravel

# Variabili ambiente essenziali
ENV APP_ENV=local
ENV DEBIAN_FRONTEND=noninteractive
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_NO_INTERACTION=1
ENV PHPREDIS_VERSION=6.1.0RC1

# Aggiungi repository PHP necessari per Ubuntu 24.04
RUN apt-get update && apt-get install -y \
    software-properties-common \
    gnupg \
    curl \
    apt-transport-https \
    ca-certificates \
    && curl -sSL https://packages.sury.org/php/apt.gpg | gpg --dearmor -o /usr/share/keyrings/sury-php.gpg \
    && echo "deb [signed-by=/usr/share/keyrings/sury-php.gpg] https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/sury-php.list \
    && apt-get update -y

# Aggiornamento sistema e installazione dipendenze di sistema
RUN apt-get install -y \
    curl \
    git \
    unzip \
    zip \
    vim \
    nano \
    htop \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libwebp-dev \
    libicu-dev \
    libxml2-dev \
    libzip-dev \
    libonig-dev \
    libcurl4-openssl-dev \
    libgmp-dev \
    libldap2-dev \
    default-mysql-client \
    iputils-ping \
    net-tools \
    libmagickwand-dev \
    imagemagick \
    pkg-config \
    zlib1g-dev \
    libpq-dev \
    && apt-get autoremove -y \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Configurazione e installazione estensioni PHP per Laravel 12
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-configure intl \
    && docker-php-ext-install -j$(nproc) \
        bcmath \
        pdo_mysql \
        pdo_pgsql \
        gd \
        intl \
        zip \
        opcache \
        exif \
        pcntl \
        sockets

# Installazione Redis extension via PECL
RUN pecl install redis-${PHPREDIS_VERSION} \
    && docker-php-ext-enable redis

# Installazione Imagick via PECL
RUN pecl install imagick \
    && docker-php-ext-enable imagick

# Installazione Composer 2.8+ (versione più recente)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer --version

# Configurazione utente per compatibilità Linux Mint 22.1
RUN groupadd -g ${GID} ${USER} \
    && useradd -u ${UID} -g ${USER} -s /bin/bash -m ${USER} \
    && id ${USER}

# Configurazione directory di lavoro
WORKDIR /var/www/html

# Configurazione permessi per storage e cache Laravel
RUN mkdir -p storage/logs storage/framework/{cache,sessions,views} bootstrap/cache \
    && chown -R ${USER}:${USER} /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Configurazione PHP-FPM per utente non-root
RUN sed -i "s/user = www-data/user = ${USER}/g" /usr/local/etc/php-fpm.d/www.conf \
    && sed -i "s/group = www-data/group = ${USER}/g" /usr/local/etc/php-fpm.d/www.conf \
    && sed -i "s/listen.owner = www-data/listen.owner = ${USER}/g" /usr/local/etc/php-fpm.d/www.conf \
    && sed -i "s/listen.group = www-data/listen.group = ${USER}/g" /usr/local/etc/php-fpm.d/www.conf

# =========================================
# STAGE DEVELOPMENT: Ambiente sviluppo
# =========================================
FROM base AS development

# Variabili specifiche per development
ARG XDEBUG_ENABLED=true
ARG XDEBUG_MODE=develop,coverage,debug,profile
ARG XDEBUG_HOST=host.docker.internal
ARG XDEBUG_IDE_KEY=VSCODE
ARG XDEBUG_LOG=/tmp/xdebug.log
ARG XDEBUG_LOG_LEVEL=0

ENV APP_ENV=local
ENV APP_DEBUG=true

# Installazione Xdebug per debugging
RUN if [ "${XDEBUG_ENABLED}" = "true" ]; then \
        pecl install xdebug \
        && docker-php-ext-enable xdebug \
        && echo "xdebug.mode=${XDEBUG_MODE}" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
        && echo "xdebug.idekey=${XDEBUG_IDE_KEY}" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
        && echo "xdebug.log=${XDEBUG_LOG}" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
        && echo "xdebug.log_level=${XDEBUG_LOG_LEVEL}" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
        && echo "xdebug.client_host=${XDEBUG_HOST}" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
        && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
        && echo "xdebug.discover_client_host=true" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    fi

# Installazione Node.js 22 LTS per Vue.js 3.5 e TailwindCSS 4.0
ARG NODE_MAJOR=22
RUN mkdir -p /etc/apt/keyrings \
    && curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg \
    && echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_${NODE_MAJOR}.x nodistro main" > /etc/apt/sources.list.d/nodesource.list \
    && apt-get update && apt-get install -y nodejs \
    && corepack enable \
    && corepack prepare pnpm@latest --activate \
    && node --version \
    && npm --version \
    && pnpm --version

# Configurazione PHP per development
COPY --chown=1000:1000 ./docker/php/development.ini /usr/local/etc/php/conf.d/
COPY --chown=1000:1000 ./docker/php/opcache-dev.ini /usr/local/etc/php/conf.d/
COPY --chown=1000:1000 ./docker/entrypoint-dev.sh /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh

# Switch all'utente non-root per sicurezza
USER ${USER}

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]

# =========================================
# STAGE PRODUCTION: Ambiente produzione
# =========================================
FROM base AS production

ENV APP_ENV=production
ENV APP_DEBUG=false

# Installazione dipendenze di produzione con Composer
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Copia codice applicazione
COPY --chown=${UID}:${GID} . .

# Configurazione PHP per production
COPY --chown=${UID}:${GID} ./docker/php/production.ini /usr/local/etc/php/conf.d/
COPY --chown=${UID}:${GID} ./docker/php/opcache-prod.ini /usr/local/etc/php/conf.d/

# Ottimizzazioni Laravel per production
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && php artisan event:cache

# Rimozione strumenti non necessari in production
RUN apt-get purge -y \
    git \
    vim \
    nano \
    htop \
    && apt-get autoremove -y \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Switch all'utente non-root
USER ${USER}

EXPOSE 9000

CMD ["php-fpm"]

# =========================================
# STAGE TESTING: Ambiente test
# =========================================
FROM development AS testing

ENV APP_ENV=testing

# Database SQLite per test veloci
USER root
RUN apt-get update && apt-get install -y sqlite3 \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Configurazione specifica per testing
COPY --chown=${UID}:${GID} ./docker/php/testing.ini /usr/local/etc/php/conf.d/

USER ${USER}

CMD ["php-fpm"]
