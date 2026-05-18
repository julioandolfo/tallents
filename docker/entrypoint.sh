#!/bin/sh
set -e

cd /var/www/html

echo ""
echo "╔══════════════════════════════════════╗"
echo "║         Tallents RH — Deploy         ║"
echo "╚══════════════════════════════════════╝"
echo ""

# ─── 1. .env — sincroniza variáveis do ambiente do container ──────────────────
[ ! -f ".env" ] && cp .env.example .env

_set() {
    KEY="$1"; VAL="$2"
    [ -z "$VAL" ] && return
    if grep -q "^${KEY}=" .env 2>/dev/null; then
        sed -i "s|^${KEY}=.*|${KEY}=${VAL}|" .env
    else
        echo "${KEY}=${VAL}" >> .env
    fi
}

# Sobrescreve com o que vier do docker-compose / Coolify
_set APP_NAME        "${APP_NAME:-Tallents RH}"
_set APP_ENV         "${APP_ENV:-production}"
_set APP_DEBUG       "${APP_DEBUG:-false}"
_set APP_URL         "${APP_URL:-http://localhost}"
_set APP_TIMEZONE    "${APP_TIMEZONE:-America/Sao_Paulo}"

_set DB_CONNECTION   "${DB_CONNECTION:-mysql}"
_set DB_HOST         "${DB_HOST:-mysql}"
_set DB_PORT         "${DB_PORT:-3306}"
_set DB_DATABASE     "${DB_DATABASE:-tallents_rh}"
_set DB_USERNAME     "${DB_USERNAME:-tallents}"
_set DB_PASSWORD     "${DB_PASSWORD:-T4ll3nts_DB_2024!}"

_set REDIS_HOST      "${REDIS_HOST:-redis}"
_set REDIS_PORT      "${REDIS_PORT:-6379}"
_set REDIS_PASSWORD  "${REDIS_PASSWORD:-T4ll3nts_RD_2024!}"
_set REDIS_CLIENT    "phpredis"

_set CACHE_STORE     "redis"
_set SESSION_DRIVER  "redis"
_set QUEUE_CONNECTION "redis"

_set MAIL_MAILER     "${MAIL_MAILER:-log}"
_set MAIL_HOST       "${MAIL_HOST:-}"
_set MAIL_PORT       "${MAIL_PORT:-587}"
_set MAIL_USERNAME   "${MAIL_USERNAME:-}"
_set MAIL_PASSWORD   "${MAIL_PASSWORD:-}"
_set MAIL_FROM_ADDRESS "${MAIL_FROM_ADDRESS:-noreply@tallents.com.br}"
_set MAIL_FROM_NAME  "Tallents RH"

echo "✅ Variáveis configuradas"

# ─── 2. APP_KEY ───────────────────────────────────────────────────────────────
CURRENT_KEY=$(grep "^APP_KEY=" .env | cut -d= -f2 | tr -d '"')
if [ -n "${APP_KEY}" ]; then
    _set APP_KEY "${APP_KEY}"
elif [ -z "${CURRENT_KEY}" ]; then
    echo "🔑 Gerando APP_KEY..."
    php artisan key:generate --force --ansi
fi
echo "✅ APP_KEY pronto"

# ─── 3. Aguarda banco ─────────────────────────────────────────────────────────
echo "⏳ Aguardando MySQL em ${DB_HOST:-mysql}..."
TRIES=0
until php artisan db:show --no-interaction > /dev/null 2>&1; do
    TRIES=$((TRIES + 1))
    [ "$TRIES" -ge 30 ] && echo "❌ MySQL não respondeu em 60s" && exit 1
    sleep 2
done
echo "✅ MySQL disponível"

# ─── 4. Migrations ────────────────────────────────────────────────────────────
echo "📦 Migrations..."
php artisan migrate --force --no-interaction
echo "✅ Migrations OK"

# ─── 5. Seeders (apenas primeira instalação) ──────────────────────────────────
USER_COUNT=$(php artisan tinker --execute="echo \App\Models\Usuario::count();" 2>/dev/null | tail -1)
if [ "${USER_COUNT}" = "0" ] || [ -z "${USER_COUNT}" ]; then
    echo "🌱 Primeira instalação — rodando seeders..."
    php artisan db:seed --force --no-interaction
    echo ""
    echo "  ┌───────────────────────────────────────────┐"
    echo "  │  ✅ Instalação concluída!                  │"
    echo "  │                                            │"
    echo "  │  Acesso inicial:                           │"
    echo "  │  E-mail : admin@tallents.com.br            │"
    echo "  │  Senha  : Tallents@2024                    │"
    echo "  │                                            │"
    echo "  │  ⚠️  Troque a senha no primeiro acesso!    │"
    echo "  └───────────────────────────────────────────┘"
    echo ""
fi

# ─── 6. Storage link ──────────────────────────────────────────────────────────
php artisan storage:link --force > /dev/null 2>&1 || true
echo "✅ Storage link OK"

# ─── 7. Cache de produção ─────────────────────────────────────────────────────
echo "⚡ Gerando caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
echo "✅ Caches gerados"

# ─── 8. Permissões ────────────────────────────────────────────────────────────
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

echo ""
echo "🚀 Pronto! Tallents RH no ar."
echo ""

exec "$@"
