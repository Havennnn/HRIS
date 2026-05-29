# ─── Stage 1: Build frontend assets ──────────────────────────────────────────
FROM node:22-alpine AS frontend

WORKDIR /build
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

# ─── Stage 2: PHP runtime ────────────────────────────────────────────────────
FROM serversideup/php:8.2-fpm-nginx-alpine AS runtime

USER root

# Install PHP extensions needed
RUN install-php-extensions pcntl pdo_pgsql

# Copy built frontend
COPY --from=frontend /build /var/www/html
RUN chown -R webuser:webgroup /var/www/html

# ─── Supervisor: Queue Worker ────────────────────────────────────────────────
COPY --chown=webuser:webgroup docker/supervisor/queue-worker.conf /etc/supervisor/conf.d/queue-worker.conf
COPY --chown=webuser:webgroup docker/supervisor/inertia-ssr.conf /etc/supervisor/conf.d/inertia-ssr.conf
COPY --chown=webuser:webgroup docker/supervisor/scheduler.conf /etc/supervisor/conf.d/scheduler.conf

# ─── Custom entrypoint ───────────────────────────────────────────────────────
COPY --chown=webuser:webgroup docker-entrypoint.sh /docker-entrypoint.sh
RUN chmod +x /docker-entrypoint.sh

USER webuser
