# Documentación Completa de Pruebas

## Tabla de Contenidos
1. [Introducción](#1-introducción)
2. [Estructura de la Documentación](#2-estructura-de-la-documentación)
3. [Controladores](#3-controladores)
   - [3.1. ProjectController](#31-projectcontroller)
   - [3.2. UserController](#32-usercontroller)
   - [3.3. ContentController](#33-contentcontroller)
   - [3.4. VersionController](#34-versioncontroller)
   - [3.5. Auth Controllers](#35-auth-controllers)
   - [3.6. Otros Controladores](#36-otros-controladores)
4. [Pruebas con Tinker](#4-pruebas-con-tinker)
5. [Pruebas con Postman](#5-pruebas-con-postman)
6. [Consideraciones Adicionales](#6-consideraciones-adicionales)

## 1. Introducción

Este documento proporciona una guía completa para probar todos los controladores de la aplicación Backend_ABI. Incluye ejemplos para Tinker (consola) y Postman (API) para cada endpoint.

## 2. Estructura de la Documentación

Cada controlador se documentará con:
- Descripción de su propósito
- Endpoints disponibles
- Ejemplos de Tinker
- Ejemplos de Postman
- Consideraciones especiales

## 3. Controladores

### 3.1. ProjectController

**Descripción**: Gestiona el ciclo de vida de las propuestas de proyectos para estudiantes y profesores.

#### Endpoints principales:
- `GET /projects` - Listar proyectos
- `POST /projects` - Crear proyecto
- `GET /projects/{id}` - Ver detalle de proyecto
- `PUT /projects/{id}` - Actualizar proyecto
- `DELETE /projects/{id}` - Eliminar proyecto
- `POST /projects/{id}/restore` - Restaurar proyecto
- `GET /projects/meta` - Obtener metadatos

#### Ejemplo Tinker:
```php
// Crear proyecto
$project = App\Models\Project::create([
    'title' => 'Nuevo Proyecto',
    'thematic_area_id' => 1,
    'project_status_id' => 1
]);

// Asignar profesores y estudiantes
$project->professors()->attach([1, 2]);
$project->students()->attach([3, 4]);
```

#### Ejemplo Postman:
```
POST /api/projects
{
    "title": "Proyecto de Investigación",
    "thematic_area_id": 1,
    "project_status_id": 1,
    "professor_ids": [1, 2],
    "student_ids": [3, 4]
}
```

### 3.2. UserController

**Descripción**: Gestiona los usuarios del sistema.

#### Endpoints principales:
- `GET /users` - Listar usuarios
- `POST /users` - Crear usuario
- `GET /users/{id}` - Ver usuario
- `PUT /users/{id}` - Actualizar usuario
- `DELETE /users/{id}` - Eliminar usuario

#### Ejemplo Tinker:
```php
// Crear usuario
$user = App\Models\User::create([
    'name' => 'Nuevo Usuario',
    'email' => 'nuevo@ejemplo.com',
    'password' => bcrypt('contraseña')
]);
```

#### Ejemplo Postman:
```
POST /api/users
{
    "name": "Nuevo Usuario",
    "email": "nuevo@ejemplo.com",
    "password": "contraseña",
    "password_confirmation": "contraseña"
}
```

### 3.3. ContentController

**Descripción**: Gestiona el contenido de los proyectos.

#### Endpoints principales:
- `GET /contents` - Listar contenidos
- `POST /contents` - Crear contenido
- `GET /contents/{id}` - Ver contenido
- `PUT /contents/{id}` - Actualizar contenido
- `DELETE /contents/{id}` - Eliminar contenido

#### Ejemplo Tinker:
```php
// Crear contenido
$content = App\Models\Content::create([
    'title' => 'Documento de Investigación',
    'content' => 'Contenido del documento...',
    'project_id' => 1
]);
```

### 3.4. VersionController

**Descripción**: Gestiona las versiones de los proyectos.

#### Endpoints principales:
- `GET /projects/{project}/versions` - Listar versiones
- `POST /projects/{project}/versions` - Crear versión
- `GET /projects/{project}/versions/{version}` - Ver versión

#### Ejemplo Tinker:
```php
// Crear versión
$version = App\Models\Version::create([
    'name' => 'Versión 1.0',
    'description' => 'Primera versión estable',
    'project_id' => 1
]);
```

### 3.5. Auth Controllers

#### 3.5.1. LoginController
- `POST /login` - Iniciar sesión
- `POST /logout` - Cerrar sesión

#### 3.5.2. RegisterController
- `POST /register` - Registrar nuevo usuario

#### 3.5.3. ForgotPasswordController
- `POST /password/email` - Solicitar restablecimiento
- `POST /password/reset` - Restablecer contraseña

### 3.6. Otros Controladores

#### DepartmentController
- Gestión de departamentos

#### CityController
- Gestión de ciudades

#### ProgramController
- Gestión de programas académicos

#### ResearchGroupController
- Gestión de grupos de investigación

## 4. Pruebas con Tinker

### Configuración inicial
```php
// Autenticarse como administrador
$user = App\Models\User::where('email', 'admin@ejemplo.com')->first();
auth()->login($user);

// Obtener instancia de un modelo
$project = App\Models\Project::first();
```

### Ejemplos de consultas comunes
```php
// Proyectos con sus relaciones
$projects = App\Models\Project::with(['professors', 'students', 'thematicArea'])->get();

// Usuarios con rol de profesor
$professors = App\Models\User::whereHas('professor')->get();

// Contenidos recientes
$recentContents = App\Models\Content::latest()->take(5)->get();
```

## 5. Pruebas con Postman

### Colección de ejemplo
```json
{
  "info": {
    "name": "Backend_ABI API",
    "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
  },
  "item": [
    {
      "name": "Autenticación",
      "item": [
        {
          "name": "Login",
          "request": {
            "method": "POST",
            "header": [{"key": "Content-Type", "value": "application/json"}],
            "body": {
              "mode": "raw",
              "raw": "{\n    \"email\": \"admin@ejemplo.com\",\n    \"password\": \"password\"\n}"
            },
            "url": {"raw": "{{base_url}}/api/login", "host": ["{{base_url}}"], "path": ["api", "login"]}
          }
        }
      ]
    },
    {
      "name": "Proyectos",
      "item": [
        {
          "name": "Listar Proyectos",
          "request": {
            "method": "GET",
            "header": [{"key": "Authorization", "value": "Bearer {{token}}"}],
            "url": {"raw": "{{base_url}}/api/projects", "host": ["{{base_url}}"], "path": ["api", "projects"]}
          }
        }
      ]
    }
  ]
}
```

## 6. Consideraciones Adicionales

### Variables de Entorno
```
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=backend_abi
DB_USERNAME=root
DB_PASSWORD=
```

### Comandos Útiles
```bash
# Ejecutar pruebas
php artisan test

# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Generar documentación de API (si está configurado)
php artisan l5-swagger:generate
```

### Seguridad
- Todos los endpoints (excepto login/register) deben incluir el token de autenticación
- Usar HTTPS en producción
- Implementar rate limiting
- Validar todas las entradas

### Rendimiento
- Usar eager loading para relaciones
- Implementar caché para datos estáticos
- Paginar resultados grandes
- Optimizar consultas con índices

---

Esta documentación proporciona una visión general de las pruebas para todos los controladores. Para información más detallada sobre cada controlador, consulta la documentación específica de cada uno.
