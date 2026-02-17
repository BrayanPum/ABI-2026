# Documentación de Pruebas - Módulo de Proyectos

## Tabla de Contenidos
1. [Introducción](#1-introducción)
2. [Configuración Inicial](#2-configuración-inicial)
3. [Pruebas con Tinker](#3-pruebas-con-tinker)
   - [3.1. Creación de Datos de Prueba](#31-creación-de-datos-de-prueba)
   - [3.2. Operaciones CRUD Básicas](#32-operaciones-crud-básicas)
   - [3.3. Gestión de Relaciones](#33-gestión-de-relaciones)
4. [Pruebas con Postman](#4-pruebas-con-postman)
   - [4.1. Colección de Postman](#41-colección-de-postman)
   - [4.2. Ejecución de Pruebas](#42-ejecución-de-pruebas)
5. [Casos de Prueba](#5-casos-de-prueba)
   - [5.1. Creación de Proyecto](#51-creación-de-proyecto)
   - [5.2. Actualización de Proyecto](#52-actualización-de-proyecto)
   - [5.3. Eliminación Lógica](#53-eliminación-lógica)
   - [5.4. Restauración de Proyecto](#54-restauración-de-proyecto)
   - [5.5. Filtrado de Proyectos](#55-filtrado-de-proyectos)
6. [Consideraciones Adicionales](#6-consideraciones-adicionales)

## 1. Introducción

Este documento proporciona una guía completa para probar el módulo de Proyectos utilizando tanto Tinker (para pruebas rápidas en consola) como Postman (para pruebas de API). El módulo permite gestionar proyectos académicos, incluyendo su relación con profesores, estudiantes, áreas temáticas y estados.

## 2. Configuración Inicial

Antes de comenzar, asegúrate de tener:

1. Entorno Laravel configurado y funcionando
2. Base de datos configurada con las migraciones ejecutadas
3. Tinker accesible a través de `php artisan tinker`
4. Postman o similar para pruebas de API
5. Usuarios de prueba creados con los roles adecuados

## 3. Pruebas con Tinker

### 3.1. Creación de Datos de Prueba

```php
// Crear datos de catálogo
$department = App\Models\Department::create(['name' => 'Antioquia']);
$city = App\Models\City::create(['name' => 'Medellín', 'department_id' => $department->id]);

$researchGroup = App\Models\ResearchGroup::create([
    'name' => 'Grupo de Investigación Educativa',
    'initials' => 'GIE',
    'description' => 'Grupo enfocado en investigación educativa'
]);

$program = App\Models\Program::create([
    'code' => '1234',
    'name' => 'Licenciatura en Educación',
    'research_group_id' => $researchGroup->id
]);

$cityProgram = App\Models\CityProgram::create([
    'city_id' => $city->id,
    'program_id' => $program->id
]);

$investigationLine = App\Models\InvestigationLine::create([
    'name' => 'Tecnologías Educativas',
    'description' => 'Línea de investigación en tecnologías para la educación',
    'research_group_id' => $researchGroup->id
]);

$thematicArea = App\Models\ThematicArea::create([
    'name' => 'Aprendizaje en Línea',
    'description' => 'Tecnologías y metodologías para el aprendizaje en línea',
    'investigation_line_id' => $investigationLine->id
]);

$status = App\Models\ProjectStatus::create([
    'name' => 'En Progreso',
    'description' => 'Proyecto en fase de desarrollo'
]);

// Crear usuario profesor
$professorUser = App\Models\User::create([
    'email' => 'profesor@example.com',
    'password' => bcrypt('password'),
    'role' => 'professor'
]);

$professor = App\Models\Professor::create([
    'card_id' => 'P12345',
    'name' => 'Ana',
    'last_name' => 'Gómez',
    'phone' => '3001234567',
    'city_program_id' => $cityProgram->id,
    'user_id' => $professorUser->id,
    'committee_leader' => false
]);

// Crear usuario estudiante
$studentUser = App\Models\User::create([
    'email' => 'estudiante@example.com',
    'password' => bcrypt('password'),
    'role' => 'student'
]);

$student = App\Models\Student::create([
    'card_id' => 'E54321',
    'name' => 'Carlos',
    'last_name' => 'Pérez',
    'phone' => '3012345678',
    'semester' => 6,
    'city_program_id' => $cityProgram->id,
    'user_id' => $studentUser->id
]);
```

### 3.2. Operaciones CRUD Básicas

**Crear un proyecto**:
```php
$project = App\Models\Project::create([
    'title' => 'Plataforma de Aprendizaje Virtual',
    'evaluation_criteria' => 'Implementación de funcionalidades clave',
    'thematic_area_id' => $thematicArea->id,
    'project_status_id' => $status->id
]);

// Asignar profesor y estudiante
$project->professors()->attach([$professor->id]);
$project->students()->attach([$student->id]);
```

**Consultar un proyecto**:
```php
$project = App\Models\Project::with(['professors', 'students', 'thematicArea', 'status'])->find(1);
```

**Actualizar un proyecto**:
```php
$project = App\Models\Project::find(1);
$project->update([
    'title' => 'Plataforma de Aprendizaje Virtual 2.0',
    'evaluation_criteria' => 'Nuevos criterios de evaluación'
]);
```

**Eliminación lógica**:
```php
$project->delete(); // Soft delete
$project->restore(); // Restaurar
$project->forceDelete(); // Eliminación permanente (solo si no hay versiones)
```

### 3.3. Gestión de Relaciones

**Añadir múltiples profesores/estudiantes**:
```php
$project->professors()->sync([1, 2, 3]);
$project->students()->sync([1, 2, 3, 4]);
```

**Consultar proyectos por profesor/estudiante**:
```php
// Proyectos de un profesor
$projects = App\Models\Project::whereHas('professors', function($q) {
    $q->where('professors.id', 1);
})->get();

// Proyectos de un estudiante
$projects = App\Models\Project::whereHas('students', function($q) {
    $q->where('students.id', 1);
})->get();
```

## 4. Pruebas con Postman

### 4.1. Colección de Postman

Puedes importar la siguiente colección en Postman:

```json
{
    "info": {
        "name": "API Proyectos",
        "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
    },
    "item": [
        {
            "name": "Crear Proyecto",
            "request": {
                "method": "POST",
                "header": [
                    {
                        "key": "Content-Type",
                        "value": "application/json"
                    },
                    {
                        "key": "Accept",
                        "value": "application/json"
                    }
                ],
                "body": {
                    "mode": "raw",
                    "raw": "{\n    \"title\": \"Nuevo Proyecto de Investigación\",\n    \"evaluation_criteria\": \"Criterios de evaluación detallados\",\n    \"thematic_area_id\": 1,\n    \"project_status_id\": 1,\n    \"professor_ids\": [1],\n    \"student_ids\": [1]\n}"
                },
                "url": {
                    "raw": "{{base_url}}/api/projects",
                    "host": ["{{base_url}}"],
                    "path": ["api", "projects"]
                }
            }
        },
        {
            "name": "Listar Proyectos",
            "request": {
                "method": "GET",
                "header": [
                    {
                        "key": "Accept",
                        "value": "application/json"
                    }
                ],
                "url": {
                    "raw": "{{base_url}}/api/projects",
                    "host": ["{{base_url}}"],
                    "path": ["api", "projects"],
                    "query": [
                        {
                            "key": "include_deleted",
                            "value": "0"
                        }
                    ]
                }
            }
        },
        {
            "name": "Obtener Metadatos",
            "request": {
                "method": "GET",
                "header": [
                    {
                        "key": "Accept",
                        "value": "application/json"
                    }
                ],
                "url": {
                    "raw": "{{base_url}}/api/projects/meta",
                    "host": ["{{base_url}}"],
                    "path": ["api", "projects", "meta"]
                }
            }
        }
    ]
}
```

### 4.2. Variables de Entorno

Crea un entorno en Postman con las siguientes variables:

```
base_url: http://tu-dominio.local
```

## 5. Casos de Prueba

### 5.1. Creación de Proyecto

**Descripción**: Verificar que se puede crear un nuevo proyecto con asignaciones.

**Tinker**:
```php
$project = App\Models\Project::create([...]);
$project->professors()->attach([1]);
$project->students()->attach([1]);
```

**Postman**:
```
POST /api/projects
{
    "title": "Proyecto de Investigación en IA",
    "evaluation_criteria": "Implementación de algoritmos de ML",
    "thematic_area_id": 1,
    "project_status_id": 1,
    "professor_ids": [1],
    "student_ids": [1, 2]
}
```

### 5.2. Actualización de Proyecto

**Descripción**: Verificar que se puede actualizar un proyecto existente.

**Tinker**:
```php
$project = App\Models\Project::find(1);
$project->update(['title' => 'Nuevo título']);
$project->professors()->sync([1, 2]);
```

**Postman**:
```
PUT /api/projects/1
{
    "title": "Proyecto Actualizado",
    "professor_ids": [1, 2, 3]
}
```

### 5.3. Eliminación Lógica

**Descripción**: Verificar que un proyecto se marca como eliminado pero no se borra físicamente.

**Tinker**:
```php
$project = App\Models\Project::find(1);
$project->delete(); // Soft delete
$project->trashed(); // true
```

**Postman**:
```
DELETE /api/projects/1
```

### 5.4. Restauración de Proyecto

**Descripción**: Verificar que un proyecto eliminado puede ser restaurado.

**Tinker**:
```php
$project = App\Models\Project::withTrashed()->find(1);
$project->restore();
```

**Postman**:
```
POST /api/projects/1/restore
```

### 5.5. Filtrado de Proyectos

**Descripción**: Verificar que se pueden filtrar proyectos por profesor o estudiante.

**Tinker**:
```php
// Proyectos de un profesor específico
$projects = App\Models\Project::whereHas('professors', function($q) {
    $q->where('professors.id', 1);
})->get();
```

**Postman**:
```
GET /api/projects?professor_id=1
```

## 6. Consideraciones Adicionales

1. **Seguridad**: Asegúrate de que los endpoints estén protegidos con autenticación.
2. **Validaciones**: Todos los campos requeridos deben ser validados tanto en frontend como en backend.
3. **Rendimiento**: Para listados con muchas relaciones, considera implementar paginación.
4. **Pruebas unitarias**: Complementa estas pruebas con pruebas unitarias automatizadas.
5. **Documentación**: Mantén actualizada la documentación de la API con cualquier cambio.

---

Este documento proporciona una guía completa para probar el módulo de Proyectos tanto en desarrollo como en entornos de prueba. Asegúrate de adaptar los ejemplos según las necesidades específicas de tu implementación.
