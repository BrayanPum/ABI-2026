#!/bin/bash
set -euo pipefail  # Strict error handling

# Load environment variables from .env (supports spaces and special characters)
set -a
source .env
set +a

# Step 1: Run database migrations and seeders
echo "==> Ejecutando migraciones y seeders..."
php artisan migrate --seed

# Step 2: Process SQL file with secure placeholder replacements
echo "==> Creando roles..."
temp_file="temp_roles.sql"

# Use sed to replace placeholders with .env values (handles special characters)
sed -e "s|{{DB_USER_PASS}}|$DB_USER_PASS|g" \
    -e "s|{{DB_DATABASE}}|$DB_DATABASE|g" \
    -e "s|{{DB_STUDENT_PASS}}|$DB_STUDENT_PASS|g" \
    -e "s|{{DB_PROFESSOR_PASS}}|$DB_PROFESSOR_PASS|g" \
    -e "s|{{DB_RESEARCH_PASS}}|$DB_RESEARCH_PASS|g" \
    "database/sql/roles.sql" > "$temp_file"

# Step 3: Execute SQL in MySQL (with error capture)
echo "==> Ejecutando SQL en MySQL..."
if ! mysql -u"$DB_USERNAME" -p"$DB_PASSWORD" -h"$DB_HOST" -P"$DB_PORT" "$DB_DATABASE" < "$temp_file"; then
    echo "❌ Error al ejecutar el archivo SQL. Contenido del archivo temporal:"
    cat "$temp_file"
    exit 1
fi

# Clean up temporary file
rm -f "$temp_file"
echo "==> Base de datos inicializada correctamente 🎉"