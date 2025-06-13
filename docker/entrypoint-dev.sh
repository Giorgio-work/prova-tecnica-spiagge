#!/bin/bash
set -e

echo "🏖️ Inizializzazione Gestione Spiagge..."

# STEP 1: Verifica vendor prima di tutto
if [ ! -f vendor/autoload.php ]; then
    echo "📦 Installazione Composer..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
    echo "✅ Composer completato"
fi

# STEP 2: Crea .env con verifiche rigorose
echo "📝 Configurazione .env..."
if [ ! -f .env ]; then
    if [ ! -f .env.example ]; then
        echo "❌ ERRORE: .env.example non trovato!"
        exit 1
    fi

    cp .env.example .env
    echo "✅ .env creato"

    # Configurazioni Docker
    sed -i 's/DB_HOST=.*/DB_HOST=mysql/' .env
    sed -i 's/DB_PASSWORD=.*/DB_PASSWORD=Predator_root1234/' .env
    sed -i 's/REDIS_HOST=.*/REDIS_HOST=redis/' .env
    sed -i 's/MAIL_HOST=.*/MAIL_HOST=mailhog/' .env
    sed -i 's/MAIL_PORT=.*/MAIL_PORT=1025/' .env

    echo "✅ .env configurato per Docker"
fi

# STEP 3: Directory Laravel
mkdir -p storage/framework/{views,cache,sessions} storage/logs bootstrap/cache
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# STEP 4: APP_KEY con test
echo "🔑 Configurazione APP_KEY..."
if ! grep -q "APP_KEY=base64:" .env; then
    echo "🔑 Generazione APP_KEY..."
    php artisan key:generate --force

    # VERIFICA che sia stata creata
    if ! grep -q "APP_KEY=base64:" .env; then
        echo "❌ ERRORE: APP_KEY non generata correttamente!"
        exit 1
    fi
    echo "✅ APP_KEY generata e verificata"
else
    echo "✅ APP_KEY già presente"
fi

# STEP 5: Database con test
echo "🗄️ Test connessione database..."
max_attempts=15
attempt=0

while [ $attempt -lt $max_attempts ]; do
    if php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'OK'; } catch(Exception \$e) { echo 'FAIL'; }" 2>/dev/null | grep -q "OK"; then
        echo "✅ Database connesso!"
        DATABASE_OK=true
        break
    fi

    attempt=$((attempt + 1))
    echo "⏳ Database non pronto ($attempt/$max_attempts)..."
    sleep 3
done

# STEP 6: Migrazioni e Seeders con controlli
if [ "$DATABASE_OK" = "true" ]; then
    echo "🔄 Controllo migrazioni..."

    # Verifica se ci sono migrazioni da eseguire
    PENDING_MIGRATIONS=$(php artisan migrate:status 2>/dev/null | grep -c "No" || echo "0")

    PENDING_MIGRATIONS="${PENDING_MIGRATIONS//[$'\t\r\n ']}"

    if [ "$PENDING_MIGRATIONS" -gt "0" ] || ! php artisan migrate:status >/dev/null 2>&1; then
        echo "🔄 Esecuzione migrazioni ($PENDING_MIGRATIONS pending)..."
        php artisan migrate --force

        echo "🌱 Esecuzione seeders..."
        php artisan db:seed --force --class=DatabaseSeeder

        echo "✅ Database inizializzato!"
    else
        echo "ℹ️ Migrazioni già eseguite"
    fi

    # Verifica finale utenti
    USER_COUNT=$(php artisan tinker --execute="echo User::count();" 2>/dev/null | tail -1)
    USER_COUNT="${USER_COUNT//[$'\t\r\n ']}"
    echo "👥 Utenti nel database: $USER_COUNT"

else
    echo "⚠️ Saltando migrazioni per timeout database"
fi


# STEP 7: Pulizia e finalizzazione
echo "🧹 Cache cleanup..."
php artisan config:clear
php artisan route:clear
php artisan view:clear 2>/dev/null || true
php artisan ziggy:generate 2>/dev/null || true

echo "🚀 Laravel configurato correttamente!"

# STEP 8: Test finale prima di avviare PHP-FPM
echo "🧪 Test finale configurazione..."
if ! php artisan route:list >/dev/null 2>&1; then
    echo "❌ ERRORE: Route Laravel non funzionanti!"
    exit 1
fi

echo "✅ Tutto pronto - avvio PHP-FPM..."
exec php-fpm
