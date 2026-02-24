#!/usr/bin/bash

# Script de copie/installation pour le module de rapport financier
# Usage: bash install_rapport_module.sh

echo "========================================"
echo "Installation Module Rapport Financier"
echo "========================================"
echo ""

# Couleurs
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# 1. Exécuter migrations
echo -e "${BLUE}[1/5] Exécution des migrations...${NC}"
php artisan migrate --step

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Migrations OK${NC}"
else
    echo -e "${YELLOW}✗ Erreur migration${NC}"
    exit 1
fi

echo ""

# 2. Créer dossiers vues s'ils n'existent pas
echo -e "${BLUE}[2/5] Vérification des dossiers...${NC}"
mkdir -p resources/views/backend/pages/rapports/charges
echo -e "${GREEN}✓ Dossiers OK${NC}"

echo ""

# 3. Vérifier les fichiers créés
echo -e "${BLUE}[3/5] Vérification des fichiers...${NC}"

FILES_TO_CHECK=(
    "app/Models/Charge.php"
    "app/Services/RapportProprietaireService.php"
    "app/Services/RapportAgenceService.php"
    "resources/views/backend/pages/rapports/proprietaire.blade.php"
    "resources/views/backend/pages/rapports/agence.blade.php"
    "resources/views/backend/pages/rapports/charges/index.blade.php"
    "resources/views/backend/pages/rapports/charges/create.blade.php"
    "resources/views/backend/pages/rapports/charges/edit.blade.php"
)

ALL_FILES_OK=true
for file in "${FILES_TO_CHECK[@]}"; do
    if [ -f "$file" ]; then
        echo -e "${GREEN}✓${NC} $file"
    else
        echo -e "${YELLOW}✗${NC} $file MANQUANT!"
        ALL_FILES_OK=false
    fi
done

if [ "$ALL_FILES_OK" = true ]; then
    echo -e "${GREEN}✓ Tous les fichiers OK${NC}"
else
    echo -e "${YELLOW}✗ Certains fichiers manquent${NC}"
    exit 1
fi

echo ""

# 4. Test basique
echo -e "${BLUE}[4/5] Test des services...${NC}"

php -r "
use App\Services\RapportProprietaireService;
use App\Services\RapportAgenceService;

try {
    \$service1 = new RapportProprietaireService();
    \$service2 = new RapportAgenceService();
    echo 'Services chargés OK';
} catch (Exception \$e) {
    echo 'Erreur: ' . \$e->getMessage();
    exit(1);
}
"

echo -e "${GREEN}✓ Services OK${NC}"

echo ""

# 5. Nettoyer cache
echo -e "${BLUE}[5/5] Nettoyage du cache...${NC}"
php artisan cache:clear
php artisan config:clear
php artisan route:clear
echo -e "${GREEN}✓ Cache limpiado${NC}"

echo ""
echo -e "${GREEN}========================================"
echo "Installation complète ! ✓"
echo "========================================${NC}"
echo ""
echo "Prochaines étapes:"
echo "1. Accéder à: http://localhost/admin/rapports/proprietaire"
echo "2. Accéder à: http://localhost/admin/rapports/agence"
echo "3. Accéder à: http://localhost/admin/charges"
echo ""
