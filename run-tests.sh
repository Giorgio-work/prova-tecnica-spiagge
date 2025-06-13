#!/bin/bash
echo "🧪 Esecuzione test suite..."

# Prepara environment test
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan migrate:fresh --env=testing

# Esegui tests
echo "🔍 Unit Tests..."
docker-compose exec app php artisan test --testsuite=Unit

echo "🔍 Feature Tests..."
docker-compose exec app php artisan test --testsuite=Feature

echo "📊 Coverage Report..."
docker-compose exec app php artisan test --coverage

echo "✅ Tests completati!"
