# Documentación técnica de ABI

Este directorio reúne la documentación técnica del proyecto. La guía de esta página es la referencia para preparar un entorno de desarrollo; el [README principal](../README.md) ofrece una vista funcional del sistema y un inicio rápido.

## Índice

- [Instalación y configuración](#instalación-y-configuración)
- [Conexiones MySQL por rol](#conexiones-mysql-por-rol)
- [Ejecución y comprobaciones](#ejecución-y-comprobaciones)
- [Documentación generada](#documentación-generada)
- [Automatización con GitHub Actions](#automatización-con-github-actions)

## Instalación y configuración

### Requisitos

- PHP **8.2 o superior** con las extensiones requeridas por Laravel, incluido `pdo_mysql`.
- Composer.
- Node.js y npm.
- MySQL o MariaDB.
- Git.

En Windows, XAMPP es una opción compatible para proporcionar Apache, MySQL y PHP. En Linux o macOS puede usarse cualquier instalación equivalente que cumpla los requisitos.

### 1. Clonar e instalar dependencias

```bash
git clone <url-del-repositorio>
cd ABI
composer install
npm install
```

En Windows con XAMPP, asegúrate primero de que la terminal use el PHP de XAMPP:

```powershell
$env:Path = "C:\xampp\php;C:\xampp\mysql\bin;" + $env:Path
php --ini
```

La ruta mostrada por `php --ini` debe corresponder a `C:\xampp\php`.

### 2. Crear y completar `.env`

Parte siempre de la plantilla local, que no contiene secretos:

```bash
cp .env.example .env
```

En Windows PowerShell, usa:

```powershell
Copy-Item .env.example .env
```

Configura al menos la aplicación y la conexión administrativa de MySQL:

```env
APP_NAME=ABI
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=abi
DB_USERNAME=root
DB_PASSWORD=
```

No versiones `.env` ni credenciales. Para una base de datos remota, crea tu propia copia local con las variables del proveedor; no uses valores de ejemplo como si fueran secretos compartidos.

### 3. Generar la clave y preparar archivos públicos

```bash
php artisan key:generate
php artisan storage:link
```

El enlace de almacenamiento permite servir, entre otros archivos, las fotos de perfil guardadas en el disco `public`.

### 4. Inicializar la base de datos

Primero crea la base de datos indicada por `DB_DATABASE` (por ejemplo, `abi`). Después elige **una** de estas opciones:

**Esquema y datos de muestra, sin usuarios MySQL restringidos:**

```bash
php artisan migrate --seed
```

**Esquema, datos de muestra y usuarios MySQL por rol:** completa las variables de la siguiente sección y ejecuta el script apropiado:

```bash
# Linux o Git Bash
bash scripts/set-db-roles.sh

# Windows PowerShell
powershell -ExecutionPolicy Bypass -File .\scripts\set-db-roles.ps1
```

Estos scripts ejecutan `migrate --seed` y procesan `database/sql/roles.sql`. Necesitan una cuenta de MySQL con permisos para crear usuarios y asignar privilegios. No los ejecutes contra una base de datos administrada si el proveedor no permite `CREATE USER` o `GRANT`; en ese caso utiliza `php artisan migrate --seed` y adapta las credenciales de rol a las capacidades del proveedor.

### 5. Compilar e iniciar

```bash
npm run build
php artisan serve
```

La aplicación queda disponible normalmente en `http://127.0.0.1:8000`. Durante el desarrollo de frontend, ejecuta además `npm run dev` en otra terminal. Vite usa HMR en `localhost:3000`.

## Conexiones MySQL por rol

Además de `mysql`, la aplicación define las conexiones `mysql_user`, `mysql_research_staff`, `mysql_professor` y `mysql_student` en [`config/database.php`](../config/database.php). Para utilizar el esquema de privilegios por rol, añade a tu `.env` las variables que consume esa configuración y el script de roles:

```env
DB_USER_USERNAME=db_user
DB_USER_PASS=<contraseña-segura>
DB_RESEARCH_USER=db_research_staff
DB_RESEARCH_PASS=<contraseña-segura>
DB_PROFESSOR_PASS=<contraseña-segura>
DB_STUDENT_PASS=<contraseña-segura>
```

`mysql_professor` y `mysql_student` utilizan respectivamente los usuarios `db_professor` y `db_student`; `mysql_user` y `mysql_research_staff` permiten configurar el nombre de usuario mediante las variables anteriores. Conserva estas contraseñas fuera del repositorio.

## Ejecución y comprobaciones

Comandos cotidianos:

```bash
# Limpiar cachés cuando cambie la configuración o las rutas
php artisan optimize:clear

# Ejecutar la suite de pruebas
php artisan test

# Aplicar el formateador PHP
./vendor/bin/pint
```

Las pruebas están configuradas para MySQL en `phpunit.xml`; verifica que la base de datos de pruebas exista y que las credenciales disponibles puedan crear o migrar el esquema antes de ejecutarlas.

## Documentación generada

Los siguientes artefactos se generan automáticamente y sirven de consulta:

- [Rutas completas](generated/routes/all-routes.txt)
- [Rutas API](generated/routes/api-routes.txt)
- [Diagrama ERD](generated/diagrams/erd.svg)
- [Documentación API](generated/api/index.html)
- [Documentación de código](generated/code/html/index.html)

Para regenerarlos localmente, instala `doxygen` y `graphviz` si deseas incluir la documentación de código y el ERD, y ejecuta:

```bash
bash scripts/generate-docs.sh
```

El script genera los listados de rutas con Artisan; ejecuta Scribe y el generador ERD cuando sus comandos están disponibles; y ejecuta Doxygen cuando está instalado.

No edites manualmente estas carpetas generadas:

- `docs/generated/api`
- `docs/generated/code`
- `docs/generated/diagrams`
- `docs/generated/routes`

## Automatización con GitHub Actions

El workflow [`.github/workflows/auto-docs.yml`](../.github/workflows/auto-docs.yml) se ejecuta ante `push` a las ramas `main` y `docs/auto-documentacion`, y también puede iniciarse manualmente con `workflow_dispatch`.

Instala PHP 8.2, Doxygen y Graphviz, prepara Laravel y regenera `docs/generated`. Si esos artefactos cambian, el workflow crea y publica un commit automático. Los cambios que afectan únicamente `docs/generated/**` se ignoran como desencadenantes para evitar bucles.
