#!/bin/sh
# Sem `set -e`: queremos que o servidor web (supervisord) sempre suba, mesmo se
# uma etapa de bootstrap falhar. Assim o container fica "Up", o /up responde 200,
# o Traefik roteia e os erros aparecem nos logs — em vez de um crash-loop.

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
    # Remove a linha existente e regrava com aspas. O dotenv do Laravel exige
    # aspas em valores com espaço (ex.: APP_NAME="Tallents RH"); sem elas o
    # parser falha com "unexpected whitespace" e toda requisição dá 500.
    if [ -f .env ]; then
        grep -v "^${KEY}=" .env > .env.tmp 2>/dev/null || true
        mv .env.tmp .env
    fi
    printf '%s="%s"\n' "$KEY" "$VAL" >> .env
}

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
    php artisan key:generate --force --ansi || echo "⚠️  Falha ao gerar APP_KEY"
fi
echo "✅ APP_KEY pronto"

# ─── 3. Aguarda banco (não aborta o container se demorar) ─────────────────────
echo "⏳ Aguardando MySQL em ${DB_HOST:-mysql}..."
DB_OK=0
TRIES=0
while [ "$TRIES" -lt 60 ]; do
    if php artisan db:show --no-interaction > /dev/null 2>&1; then
        DB_OK=1
        break
    fi
    TRIES=$((TRIES + 1))
    sleep 2
done

if [ "$DB_OK" = "1" ]; then
    echo "✅ MySQL disponível"

    # ─── 4. Migrations ────────────────────────────────────────────────────────
    echo "📦 Migrations..."
    php artisan migrate --force --no-interaction || echo "⚠️  Migrations falharam — verifique os logs"

    # ─── 5. Seeders (apenas primeira instalação) ──────────────────────────────
    USER_COUNT=$(php artisan tinker --execute="echo \App\Models\Usuario::count();" 2>/dev/null | tail -1)
    if [ "${USER_COUNT}" = "0" ] || [ -z "${USER_COUNT}" ]; then
        echo "🌱 Primeira instalação — rodando seeders..."
        php artisan db:seed --force --no-interaction || echo "⚠️  Seed falhou"
        echo ""
        echo "  ┌───────────────────────────────────────────┐"
        echo "  │  ✅ Instalação concluída!                  │"
        echo "  │  E-mail : admin@tallents.com.br            │"
        echo "  │  Senha  : Tallents@2024                    │"
        echo "  │  ⚠️  Troque a senha no primeiro acesso!    │"
        echo "  └───────────────────────────────────────────┘"
        echo ""
    fi

    # ─── 5b. Importação dos dados do sistema antigo (uma única vez) ────────────
    # A trava fica no volume persistente storage/app/public, então roda só no
    # primeiro deploy — sem apagar/reimportar dados a cada redeploy.
    LEGADO_DUMP="database/legado/privus_rh.sql"
    LEGADO_MARKER="storage/app/public/.legado_migrado"
    if [ -f "$LEGADO_DUMP" ] && [ ! -f "$LEGADO_MARKER" ]; then
        echo "📥 Importando dados do sistema antigo (rh-privus)..."
        if php artisan migrar:legado "$LEGADO_DUMP" --force --no-interaction; then
            mkdir -p storage/app/public
            date > "$LEGADO_MARKER"
            echo "✅ Dados do sistema antigo importados."
        else
            echo "⚠️  Importação do legado falhou — verifique os logs."
        fi
    fi
else
    echo "⚠️  MySQL indisponível após 120s — subindo o servidor mesmo assim."
    echo "    As migrations rodarão no próximo deploy/restart quando o banco responder."
fi

# ─── 6. Storage link ──────────────────────────────────────────────────────────
php artisan storage:link --force > /dev/null 2>&1 || true
echo "✅ Storage link OK"

# ─── 7. Cache de produção (best-effort) ───────────────────────────────────────
echo "⚡ Gerando caches..."
php artisan config:cache 2>/dev/null || echo "⚠️  config:cache falhou"
php artisan route:cache  2>/dev/null || echo "⚠️  route:cache falhou"
php artisan view:cache   2>/dev/null || echo "⚠️  view:cache falhou"
php artisan event:cache  2>/dev/null || true
echo "✅ Caches processados"

# ─── 8. Permissões ────────────────────────────────────────────────────────────
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

echo ""
echo "🚀 Subindo servidor (nginx + php-fpm)..."
echo ""

exec "$@"
