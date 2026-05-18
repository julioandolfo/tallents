#!/bin/sh
set -e

echo "🚀 Tallents RH — iniciando..."

# Aguarda o banco de dados ficar disponível
echo "⏳ Aguardando banco de dados..."
until php artisan db:show --no-interaction 2>/dev/null; do
    sleep 2
done
echo "✅ Banco disponível"

# Roda migrations
echo "📦 Rodando migrations..."
php artisan migrate --force --no-interaction

# Cria link simbólico do storage
echo "🔗 Criando link do storage..."
php artisan storage:link --force 2>/dev/null || true

# Otimiza para produção
echo "⚡ Otimizando..."
php artisan optimize
php artisan view:cache
php artisan event:cache

echo "✅ Inicialização concluída"

exec "$@"
