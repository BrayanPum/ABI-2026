#!/bin/bash
set -euo pipefail

# Cargar variables del .env
set -a
source .env
set +a

# Paso 1: Ejecutar migraciones y seeders (con usuario sail)
echo "==> Ejecutando migraciones y seeders..."
./vendor/bin/sail artisan migrate --seed

# Paso 2: Procesar el archivo SQL
echo "==> Creando roles..."
temp_file="temp_roles.sql"

sed -e "s|{{DB_USER_PASS}}|$DB_USER_PASS|g" \
    -e "s|{{DB_DATABASE}}|$DB_DATABASE|g" \
    -e "s|{{DB_STUDENT_PASS}}|$DB_STUDENT_PASS|g" \
    -e "s|{{DB_PROFESSOR_PASS}}|$DB_PROFESSOR_PASS|g" \
    -e "s|{{DB_RESEARCH_PASS}}|$DB_RESEARCH_PASS|g" \
    "database/sql/roles.sql" > "$temp_file"

# Paso 3: Ejecutar el SQL en MySQL CON USUARIO ROOT (¡CORREGIDO!)
echo "==> Ejecutando SQL en MySQL..."
if ! ./vendor/bin/sail exec -T mysql mysql \
    -u"root" \
    -p"password" \
    -h"$DB_HOST" \
    -P"$DB_PORT" \
    "$DB_DATABASE" < "$temp_file"; then
    echo "❌ Error al ejecutar el archivo SQL. Contenido del archivo temporal:"
    cat "$temp_file"
    rm -f "$temp_file"
    exit 1
fi

# Limpiar archivo temporal
rm -f "$temp_file"
echo "✅ Base de datos inicializada correctamente 🎉"