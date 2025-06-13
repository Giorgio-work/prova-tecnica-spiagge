#!/bin/bash
echo "🏖️ SETUP PRIMO AVVIO - Gestione Spiagge"
echo "======================================"

# Cleanup completo
echo "🧹 Pulizia ambiente..."
docker compose down -v 2>/dev/null || true
rm -f .env
docker run --rm -v $(pwd):/app -w /app alpine rm -rf vendor/ node_modules/ 2>/dev/null || true

# Avvio automatico
echo "🚀 Avvio ambiente completo..."
docker compose up -d

echo ""
echo "🎉 SETUP COMPLETATO!"
echo ""
echo "📱 Accessi disponibili:"
echo "   🏖️  App: http://localhost:8000"
echo "   🗄️  phpMyAdmin: http://localhost:8080"
echo "   📧 MailHog: http://localhost:8025"
echo ""
echo "👤 Credenziali test:"
echo "   Email: admin@spiagge.com"
echo "   Password: password123"
echo ""
echo "📊 Verifica popolamento DB:"
echo "   docker compose exec app php artisan tinker --execute=\"echo 'Users: ' . User::count();\""
