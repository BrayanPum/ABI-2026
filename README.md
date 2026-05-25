# ABI 

Plataforma web para la gestión y trazabilidad del banco de ideas y proyectos de grado. El sistema centraliza el registro de ideas, su evaluación por comité, la consulta del banco de ideas aprobadas, la asignación de estudiantes y el manejo de catálogos académicos relacionados con investigación.

## 🚀 Tecnologías utilizadas

### Backend
- **Laravel 10**
- **PHP 8.1 o superior**
- **MySQL / MariaDB**
- **Laravel Tinker**
- **Laravel Sanctum** para endpoints API autenticados

### Frontend
- **Blade** como motor de plantillas
- **Tablar** como base visual de la interfaz administrativa
- **Bootstrap 5.3.1**
- **Vite** para compilación de assets
- **jQuery 3.7**
- **ApexCharts**
- **Bootstrap Icons**

### Librerías complementarias
- **DomPDF**, **TCPDF**, **FPDF**
- **PhpSpreadsheet** y librerías de Excel
- **TinyMCE / Jodit** para edición enriquecida
- **Filepond** para carga de archivos

### Herramientas de desarrollo
- **Composer**
- **Node.js y npm**
- **Laravel Pint**
- **PHPUnit**
- **Faker**

## 📁 Arquitectura del Proyecto

### Estructura de Directorios
```
abi-mio/
├── app/
│   ├── Http/
│   │   └── Controllers/          # Controladores de la aplicación
│   └── Models/                   # Modelos Eloquent
├── config/                       # Archivos de configuración
├── database/
│   └── migrations/               # Migraciones de base de datos
├── public/                       # Archivos públicos y assets
├── resources/
│   ├── views/                    # Plantillas Blade
│   └── js/                       # Assets JavaScript
├── routes/
│   └── web.php                   # Rutas web de la aplicación
└── storage/                      # Almacenamiento de archivos
```

### Modelo de datos

El modelo de datos del aplicativo está orientado al banco de ideas y a la trazabilidad de proyectos de grado. Las entidades principales son estas:

#### 1. Usuarios y perfiles
- **users**: credenciales de acceso, estado del usuario y rol.
- **students**: perfil de estudiante vinculado a `users` y a un `city_program`.
- **professors**: perfil docente vinculado a `users` y a un `city_program`. El campo `committee_leader` diferencia al docente líder de comité.
- **research_staff**: perfil del personal de investigación vinculado a `users`.

#### 2. Estructura académica
- **departments**: departamentos.
- **cities**: ciudades asociadas a un departamento.
- **research_groups**: grupos de investigación.
- **programs**: programas académicos asociados a un grupo de investigación.
- **city_program**: relación entre programa y ciudad.
- **investigation_lines**: líneas de investigación asociadas a grupos de investigación.
- **thematic_areas**: áreas temáticas asociadas a líneas de investigación.

#### 3. Banco de ideas y proyectos
- **project_statuses**: estados del proyecto o idea, por ejemplo pendiente, aprobado, rechazado, devuelto para corrección y asignado.
- **projects**: idea o proyecto de grado, con título, criterios de evaluación, área temática y estado.
- **student_project**: relación muchos a muchos entre estudiantes y proyectos.
- **professor_project**: relación muchos a muchos entre docentes y proyectos.
- **versions**: versiones o snapshots de un proyecto. Cada versión puede guardar `snapshot` y el usuario que la creó.

#### 4. Marcos y contenidos
- **frameworks**: marcos de referencia.
- **content_frameworks**: contenidos definidos dentro de un framework.
- **content_framework_project**: relación entre contenidos de framework y proyectos.
- **contents**: catálogo de campos o contenidos diligenciables en una versión.
- **content_version**: valor diligenciado de un contenido dentro de una versión concreta.

### Relaciones principales

```text
users 1 --- 1 students
users 1 --- 1 professors
users 1 --- 1 research_staff

departments 1 --- N cities
research_groups 1 --- N programs
research_groups 1 --- N investigation_lines
investigation_lines 1 --- N thematic_areas
cities N --- N programs (mediante city_program)
city_program 1 --- N students
city_program 1 --- N professors

thematic_areas 1 --- N projects
project_statuses 1 --- N projects
projects N --- N students (mediante student_project)
projects N --- N professors (mediante professor_project)
projects 1 --- N versions
frameworks 1 --- N content_frameworks
projects N --- N content_frameworks (mediante content_framework_project)
versions N --- N contents (mediante content_version, almacenando value)
```

## 🛠️ Instalación y configuración

### Prerrequisitos

Para ejecutar el proyecto en local, **usa XAMPP**. No instales PHP, MySQL ni Apache por separado para evitar conflictos.

- **XAMPP**, con:
  - **PHP 8.1 o superior**
  - **MySQL o MariaDB**
  - **Apache**
- **Composer**, para instalar dependencias de PHP
- **Node.js y npm**, para compilar los assets del frontend

> **Importante:** este proyecto asume en Windows que XAMPP está instalado en `C:\xampp`. Si lo instalaste en otra ruta, debes ajustar los scripts o la variable PATH.

### Instalación local en Windows con XAMPP

#### 1. Instala XAMPP
Instala XAMPP y, al terminar, abre el panel de control de XAMPP.

#### 2. Inicia servicios
En el panel de XAMPP, inicia:
- **Apache**
- **MySQL**

#### 3. Verifica que estás usando el PHP de XAMPP
Antes de ejecutar comandos de Laravel, abre **PowerShell** y asegúrate de que `php` apunte al PHP de XAMPP.

Puedes hacerlo temporalmente en la terminal actual con:

```powershell
$env:Path = "C:\xampp\php;C:\xampp\mysql\bin;" + $env:Path
php --ini
```

La salida de `php --ini` debe apuntar a un `php.ini` dentro de `C:\xampp\php`.

> **No uses otro PHP instalado aparte**, porque eso suele causar errores como `could not find driver` al correr migraciones.

#### 4. Clona el repositorio
```bash
git clone <url-del-repositorio>
cd ABI-2026-main
```

#### 5. Instala dependencias de PHP
```bash
composer install
```

#### 6. Instala dependencias del frontend
```bash
npm install
```

#### 7. Configura el archivo `.env`
Si vas a trabajar con base de datos local:

```bash
copy .env.example .env
```

Si vas a usar base de datos en la nube:

```bash
copy .env.examplenube .env
```

#### 8. Ajusta las variables de entorno
Si trabajas en local, revisa como mínimo estos valores en `.env`:

```env
APP_NAME=ABI
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

Si tu usuario `root` en MySQL tiene contraseña, colócala en `DB_PASSWORD`.

#### 9. Genera la clave de la aplicación
```bash
php artisan key:generate
```

#### 10. Inicializa la base de datos local
Este paso solo aplica si estás usando `.env` local.

En Windows PowerShell, desde la raíz del proyecto:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\set-db-roles.ps1
```

Ese script realiza dos acciones:
- ejecuta migraciones y seeders
- crea los usuarios y permisos de MySQL usados por el sistema

> **Importante:** si vas a usar base de datos en la nube, omite este paso.

#### 11. Compila los assets
```bash
npm run build
```

#### 12. Inicia el servidor de desarrollo
```bash
php artisan serve
```

La aplicación quedará disponible en:

```text
http://127.0.0.1:8000
```

### Instalación local en Linux

1. Instala PHP 8.1+, Composer, Node.js y MySQL/MariaDB.
2. Copia `.env.example` a `.env`.
3. Ajusta credenciales de base de datos.
4. Ejecuta:

```bash
composer install
npm install
php artisan key:generate
bash scripts/set-db-roles.sh
npm run build
php artisan serve
```

## 👤 Sistema de autenticación

La autenticación principal del sistema es **web, basada en sesión**, usando los controladores de autenticación de Laravel para inicio y cierre de sesión.

### Cómo está modelada

- La tabla **users** almacena credenciales, estado y rol.
- La información personal y académica no se guarda completa en `users`, sino en tablas de perfil:
  - `students`
  - `professors`
  - `research_staff`
- El rol **committee_leader** comparte la tabla `professors`, diferenciándose por el campo `committee_leader` y por el valor del rol en `users`.

### Roles actuales del sistema

- **research_staff**
- **professor**
- **committee_leader**
- **student**

> El valor `user` existe en el enum de la tabla `users`, pero no es el rol principal del flujo funcional actual del aplicativo.

### Control de acceso

El acceso se controla con middleware:

- `auth`: exige usuario autenticado
- `role`: restringe módulos por rol

### Comportamiento actual

- El **inicio de sesión** se realiza por correo y contraseña.
- Los usuarios inactivos no pueden ingresar.
- El **registro de usuarios** está pensado para ser gestionado por **personal de investigación**.
- Todos los usuarios autenticados pueden consultar su perfil.
- La administración de usuarios y la activación/inactivación de cuentas se realiza desde el módulo de usuarios.

### API

El proyecto también expone rutas en `routes/api.php`. La autenticación API con Sanctum está disponible para los escenarios donde se requiera consumo autenticado, aunque el flujo principal del sistema es web.

## 🎯 Funcionalidades principales del proyecto

### Gestión de usuarios y perfiles
- Registro de usuarios por rol
- Edición, activación e inactivación de cuentas
- Perfiles de estudiante, docente, comité líder y personal de investigación
- Consulta de perfil del usuario autenticado

### Estructura académica e investigación
- Gestión de departamentos y ciudades
- Gestión de programas académicos
- Relación programa-ciudad
- Gestión de grupos de investigación
- Gestión de líneas de investigación
- Gestión de áreas temáticas

### Marcos y contenidos
- Gestión de frameworks
- Gestión de contenidos de framework
- Catálogo de contenidos diligenciables
- Gestión de versiones y valores de contenido por versión

### Banco de ideas y proyectos
- Registro de ideas o proyectos por estudiantes y docentes
- Asociación de docentes participantes
- Asociación de estudiantes a proyectos
- Consulta de proyectos según el rol autenticado
- Filtros por estado, búsqueda por título y filtrado por programa en algunos roles

### Evaluación por comité
- Vista de evaluación para comité líder
- Cambio de estado de la idea
- Estados manejados por el sistema: pendiente de aprobación, aprobado, rechazado, devuelto para corrección y asignado
- Registro de observaciones y criterios de evaluación

### Banco de ideas aprobadas
- Vista de ideas aprobadas para estudiantes
- Vista de ideas aprobadas para docentes
- Consulta detallada del proyecto aprobado
- Selección y asignación de idea por parte del estudiante, según reglas del estado actual

### Trazabilidad y versionamiento
- Historial de versiones por proyecto
- Snapshot de información del proyecto por versión
- Relación entre versiones y contenidos diligenciados

### Notificaciones
- Envío de correos cuando una idea es evaluada
- Plantillas diferenciadas para aprobación, rechazo o devolución para corrección

## 🎨 Interfaz de usuario

La interfaz del sistema está construida principalmente con **Blade + Tablar**, apoyándose en componentes de Bootstrap.

### Características de la interfaz
- Diseño administrativo responsivo
- Listados con filtros y paginación
- Formularios de creación y edición
- Vistas separadas según rol y módulo
- Plantillas de correo personalizadas para notificaciones

## 🔧 Comandos útiles

### Desarrollo
```bash
php artisan serve
npm run dev
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Base de datos
```bash
php artisan migrate
php artisan migrate:rollback
php artisan migrate:refresh
php artisan db:seed
```

### Producción
```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build
```

## 🚀 Despliegue

### Variables recomendadas para producción
```env
APP_ENV=production
APP_DEBUG=false
```

### Recomendaciones generales
- Configurar correctamente las conexiones de base de datos por rol si se usarán cuentas restringidas.
- Validar colas y correo si se van a usar las notificaciones de evaluación.
- Compilar assets antes de desplegar.

## 🔒 Seguridad

El sistema incorpora medidas como:

- autenticación web por sesión
- control de acceso por rol
- validación de formularios
- uso de passwords hasheadas
- usuarios de MySQL con permisos diferenciados para ciertos escenarios
- protección CSRF en formularios web

## 🤝 Contribución

1. Crea una rama para tu cambio.
2. Realiza tus ajustes.
3. Ejecuta pruebas y valida el flujo afectado.
4. Abre un Pull Request con la descripción del cambio.

## 📄 Licencia

Este proyecto está bajo la licencia MIT. Revisa el archivo `LICENSE` para más detalles.
