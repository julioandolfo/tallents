#!/bin/sh
set -e

cd /var/www/html

echo ""
echo "╔══════════════════════════════════════╗"
echo "║         Tallents RH — Deploy         ║"
echo "╚══════════════════════════════════════╝"
echo ""

# ─── 1. .env ──────────────────────────────────────────────────────────────────
if [ ! -f ".env" ]; then
    echo "📄 Criando .env a partir do .env.example..."
    cp .env.example .env
fi

# Injeta variáveis de ambiente do container no .env
# (Coolify passa as vars como env vars do sistema — escrevemos no .env para o Laravel)
_env_set() {
    KEY="$1"
    VALUE="$2"
    if [ -n "$VALUE" ]; then
        if grep -q "^${KEY}=" .env 2>/dev/null; then
            sed -i "s|^${KEY}=.*|${KEY}=${VALUE}|" .env
        else
            echo "${KEY}=${VALUE}" >> .env
        fi
    fi
}

_env_set "APP_NAME"       "${APP_NAME:-Tallents RH}"
_env_set "APP_ENV"        "${APP_ENV:-production}"
_env_set "APP_DEBUG"      "${APP_DEBUG:-false}"
_env_set "APP_URL"        "${APP_URL:-http://localhost}"
_env_set "APP_TIMEZONE"   "${APP_TIMEZONE:-America/Sao_Paulo}"

_env_set "DB_CONNECTION"  "${DB_CONNECTION:-mysql}"
_env_set "DB_HOST"        "${DB_HOST}"
_env_set "DB_PORT"        "${DB_PORT:-3306}"
_env_set "DB_DATABASE"    "${DB_DATABASE:-tallents_rh}"
_env_set "DB_USERNAME"    "${DB_USERNAME}"
_env_set "DB_PASSWORD"    "${DB_PASSWORD}"

_env_set "REDIS_HOST"     "${REDIS_HOST}"
_env_set "REDIS_PORT"     "${REDIS_PORT:-6379}"
_env_set "REDIS_PASSWORD" "${REDIS_PASSWORD}"

_env_set "CACHE_STORE"    "${CACHE_STORE:-redis}"
_env_set "SESSION_DRIVER" "${SESSION_DRIVER:-redis}"
_env_set "QUEUE_CONNECTION" "${QUEUE_CONNECTION:-redis}"

_env_set "MAIL_MAILER"    "${MAIL_MAILER:-log}"
_env_set "MAIL_HOST"      "${MAIL_HOST}"
_env_set "MAIL_PORT"      "${MAIL_PORT:-587}"
_env_set "MAIL_USERNAME"  "${MAIL_USERNAME}"
_env_set "MAIL_PASSWORD"  "${MAIL_PASSWORD}"
_env_set "MAIL_FROM_ADDRESS" "${MAIL_FROM_ADDRESS}"
_env_set "MAIL_FROM_NAME" "${MAIL_FROM_NAME:-Tallents RH}"

_env_set "ONESIGNAL_APP_ID"      "${ONESIGNAL_APP_ID}"
_env_set "ONESIGNAL_REST_API_KEY" "${ONESIGNAL_REST_API_KEY}"
_env_set "VAPID_PUBLIC_KEY"       "${VAPID_PUBLIC_KEY}"
_env_set "VAPID_PRIVATE_KEY"      "${VAPID_PRIVATE_KEY}"

echo "✅ Variáveis de ambiente configuradas"

# ─── 2. APP_KEY ───────────────────────────────────────────────────────────────
if [ -n "$APP_KEY" ]; then
    _env_set "APP_KEY" "$APP_KEY"
else
    CURRENT_KEY=$(grep "^APP_KEY=" .env | cut -d= -f2)
    if [ -z "$CURRENT_KEY" ] || [ "$CURRENT_KEY" = "SomeLongRandomKey" ]; then
        echo "🔑 Gerando APP_KEY..."
        php artisan key:generate --force --no-interaction
    fi
fi
echo "✅ APP_KEY configurado"

# ─── 3. Aguarda banco de dados ────────────────────────────────────────────────
echo "⏳ Aguardando banco de dados em ${DB_HOST:-mysql}:${DB_PORT:-3306}..."
TRIES=0
until php artisan db:show --no-interaction > /dev/null 2>&1; do
    TRIES=$((TRIES + 1))
    if [ "$TRIES" -ge 30 ]; then
        echo "❌ Banco de dados não respondeu após 60s. Abortando."
        exit 1
    fi
    sleep 2
done
echo "✅ Banco de dados disponível"

# ─── 4. Migrations ────────────────────────────────────────────────────────────
echo "📦 Rodando migrations..."
php artisan migrate --force --no-interaction
echo "✅ Migrations concluídas"

# ─── 5. Seeder (apenas na primeira instalação) ────────────────────────────────
USER_COUNT=$(php artisan tinker --execute="echo App\Models\Usuario::count();" 2>/dev/null | tail -1 || echo "0")
if [ "$USER_COUNT" = "0" ]; then
    echo "🌱 Primeira instalação detectada — rodando seeders..."
    php artisan db:seed --force --no-interaction
    echo "✅ Seeders concluídos"
    echo ""
    echo "  ┌─────────────────────────────────────────┐"
    echo "  │  Acesso inicial:                         │"
    echo "  │  E-mail: admin@tallents.com.br           │"
    echo "  │  Senha:  Tallents@2024                   │"
    echo "  │  ⚠️  Troque a senha no primeiro acesso!  │"
    echo "  └─────────────────────────────────────────┘"
    echo ""
fi

# ─── 6. Storage link ──────────────────────────────────────────────────────────
echo "🔗 Configurando storage..."
php artisan storage:link --force > /dev/null 2>&1 || true
echo "✅ Storage configurado"

# ─── 7. Otimizações de produção ───────────────────────────────────────────────
echo "⚡ Otimizando para produção..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
echo "✅ Cache gerado"

# ─── 8. Permissões finais ─────────────────────────────────────────────────────
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

echo ""
echo "🚀 Tallents RH iniciado com sucesso!"
echo ""

exec "$@"
