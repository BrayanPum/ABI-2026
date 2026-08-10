# Documentacion automatica

Este repositorio incluye un sistema de documentacion automatica para codigo PHP/Laravel, rutas, API y diagramas.

## Indice

- [Rutas completas](generated/routes/all-routes.txt)
- [Rutas API](generated/routes/api-routes.txt)
- [Diagrama ERD](generated/diagrams/erd.svg)
- [Documentacion API](generated/api/index.html)
- [Documentacion de codigo](generated/code/html/index.html)

## Que se genera automaticamente

- Listados de rutas con `php artisan route:list`.
- Documentacion API estatica con Scribe.
- Diagrama ERD de modelos Eloquent con `generate:erd`.
- Documentacion de codigo y diagramas de clases con Doxygen + Graphviz.

## Ejecucion local

1. Instala dependencias PHP del proyecto con `composer install`.
2. Asegura que Laravel pueda cargar paquetes con `php artisan package:discover --ansi`.
3. Instala `doxygen` y `graphviz` en tu maquina si quieres documentacion de codigo y ERD completos.
4. Ejecuta `bash scripts/generate-docs.sh`.

## Workflow de GitHub Actions

El workflow `auto-docs.yml` se ejecuta en cada `push` a `main` y tambien puede lanzarse manualmente con `workflow_dispatch`.

- Instala PHP 8.2 y extensiones comunes de Laravel.
- Instala `doxygen` y `graphviz`.
- Prepara un entorno Laravel minimo para poder ejecutar Artisan.
- Regenera el contenido dentro de `docs/generated`.
- Hace commit automatico solo cuando cambian archivos dentro de `docs/generated`.

Los cambios en `docs/generated/**` no vuelven a disparar el workflow para evitar bucles.

## Carpetas que no deben editarse manualmente

- `docs/generated/api`
- `docs/generated/code`
- `docs/generated/diagrams`
- `docs/generated/routes`

Estas carpetas se consideran artefactos generados y deben actualizarse ejecutando el script o el workflow.

## Notas operativas

- `config/scribe.php` esta configurado para documentar principalmente `api/*`.
- `config/erd-generator.php` desactiva la inspeccion del esquema SQL para que el ERD pueda generarse sin depender de una base de datos activa en CI.
- Si Scribe o el generador ERD no estan disponibles, el script mostrara un aviso y continuara con el resto de la generacion.
