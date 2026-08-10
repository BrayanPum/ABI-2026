#!/usr/bin/env bash

set -e

echo "[docs] Preparing generated documentation directories..."
mkdir -p docs/generated/api
mkdir -p docs/generated/code
mkdir -p docs/generated/diagrams
mkdir -p docs/generated/routes

echo "[docs] Generating route listings..."
php artisan route:list --except-vendor > docs/generated/routes/all-routes.txt
php artisan route:list --except-vendor --path=api > docs/generated/routes/api-routes.txt

if php artisan list --raw | grep -q '^scribe:generate[[:space:]]'; then
    echo "[docs] Generating API documentation with Scribe..."
    if ! php artisan scribe:generate --force; then
        echo "[docs] Scribe generation failed; continuing with the remaining documentation."
    fi
else
    echo "[docs] Scribe command not available; skipping API documentation."
fi

if php artisan list --raw | grep -q '^generate:erd[[:space:]]'; then
    echo "[docs] Generating ER diagram..."
    if ! php artisan generate:erd docs/generated/diagrams/erd.svg --format=svg; then
        echo "[docs] ER diagram generation failed; continuing with the remaining documentation."
    fi
else
    echo "[docs] ER diagram command not available; skipping ER diagram generation."
fi

if command -v doxygen >/dev/null 2>&1; then
    echo "[docs] Generating code documentation with Doxygen..."
    doxygen Doxyfile
else
    echo "[docs] Doxygen is not available; skipping code documentation generation."
fi

echo "[docs] Documentation generation finished."
