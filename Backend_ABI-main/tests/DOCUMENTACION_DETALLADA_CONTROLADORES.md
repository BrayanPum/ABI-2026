# Documentación Detallada de Controladores

## Tabla de Contenidos
1. [ProjectController](#1-projectcontroller)
2. [UserController](#2-usercontroller)
3. [ContentController](#3-contentcontroller)
4. [VersionController](#4-versioncontroller)
5. [Auth Controllers](#5-auth-controllers)
6. [FrameworkController](#6-frameworkcontroller)
7. [ProgramController](#7-programcontroller)
8. [ResearchGroupController](#8-researchgroupcontroller)
9. [InvestigationLineController](#9-investigationlinecontroller)
10. [BankApprovedIdeas Controllers](#10-bankapprovedideas-controllers)
11. [Otros Controladores](#11-otros-controladores)

---

## 1. ProjectController

**Ruta**: `app/Http/Controllers/ProjectController.php`

### Descripción
Gestiona el ciclo de vida completo de los proyectos de investigación, incluyendo creación, edición, eliminación y consulta de proyectos, así como la gestión de relaciones con profesores, estudiantes y áreas temáticas.

### Métodos Principales

#### `index(Request $request)`
- **Propósito**: Lista proyectos con filtrado opcional
- **Método HTTP**: GET
- **Ruta**: `/api/projects`
- **Parámetros de consulta**:
  - `professor_id`: Filtrar por profesor
  - `student_id`: Filtrar por estudiante
  - `status`: Filtrar por estado
  - `include_deleted`: Incluir eliminados (1/0)
  - `only_deleted`: Solo eliminados (1/0)

#### `store(Request $request)`
- **Propósito**: Crear un nuevo proyecto
- **Método HTTP**: POST
- **Ruta**: `/api/projects`
- **Body**:
  ```json
  {
    "title": "Título del Proyecto",
    "evaluation_criteria": "Criterios de evaluación",
    "thematic_area_id": 1,
    "project_status_id": 1,
    "professor_ids": [1, 2],
    "student_ids": [3, 4]
  }
  ```

#### `show(Project $project)`
- **Propósito**: Mostrar detalles de un proyecto
- **Método HTTP**: GET
- **Ruta**: `/api/projects/{project}`

#### `update(Request $request, Project $project)`
- **Propósito**: Actualizar un proyecto existente
- **Método HTTP**: PUT/PATCH
- **Ruta**: `/api/projects/{project}`
- **Body**: Similar al método store

### Ejemplos de Uso

#### Tinker
```php
// Crear proyecto con relaciones
$project = App\Models\Project::create([...]);
$project->professors()->attach([1, 2]);
$project->students()->attach([3, 4]);

// Consultar proyectos con relaciones
$projects = App\Models\Project::with(['professors', 'students', 'thematicArea'])->get();
```

#### Postman
```
GET /api/projects?professor_id=1&status=active
```

---

## 2. UserController

**Ruta**: `app/Http/Controllers/UserController.php`

### Descripción
Gestiona los usuarios del sistema, incluyendo creación, edición, eliminación y consulta de usuarios, así como la gestión de roles y permisos.

### Métodos Principales

#### `index()`
- **Propósito**: Listar usuarios
- **Método HTTP**: GET
- **Ruta**: `/api/users`

#### `store(Request $request)`
- **Propósito**: Crear un nuevo usuario
- **Método HTTP**: POST
- **Ruta**: `/api/users`
- **Body**:
  ```json
  {
    "name": "Nombre Usuario",
    "email": "usuario@ejemplo.com",
    "password": "contraseña",
    "password_confirmation": "contraseña",
    "role": "professor"
  }
  ```

---

## 3. ContentController

**Ruta**: `app/Http/Controllers/ContentController.php`

### Descripción
Gestiona el contenido asociado a los proyectos, incluyendo documentos, archivos y metadatos.

### Métodos Principales

#### `store(Request $request)`
- **Propósito**: Crear nuevo contenido
- **Método HTTP**: POST
- **Ruta**: `/api/contents`
- **Body** (multipart/form-data):
  - `title`: Título del contenido
  - `content`: Contenido (texto o archivo)
  - `project_id`: ID del proyecto
  - `content_type`: Tipo de contenido
  - `file`: Archivo adjunto (opcional)

#### `update(Request $request, Content $content)`
- **Propósito**: Actualizar contenido existente
- **Método HTTP**: PUT/PATCH
- **Ruta**: `/api/contents/{content}`

---

## 4. VersionController

**Ruta**: `app/Http/Controllers/VersionController.php`

### Descripción
Gestiona las versiones de los proyectos, permitiendo el control de versiones del contenido.

### Métodos Principales

#### `store(Request $request, Project $project)`
- **Propósito**: Crear nueva versión
- **Método HTTP**: POST
- **Ruta**: `/api/projects/{project}/versions`
- **Body**:
  ```json
  {
    "name": "Versión 1.0",
    "description": "Primera versión estable",
    "content": "Contenido de la versión..."
  }
  ```

#### `show(Project $project, Version $version)`
- **Propósito**: Mostrar detalles de versión
- **Método HTTP**: GET
- **Ruta**: `/api/projects/{project}/versions/{version}`

---

## 5. Auth Controllers

### 5.1. LoginController
- **Ruta**: `app/Http/Controllers/Auth/LoginController.php`
- **Endpoints**:
  - `POST /login` - Iniciar sesión
  - `POST /logout` - Cerrar sesión

### 5.2. RegisterController
- **Ruta**: `app/Http/Controllers/Auth/RegisterController.php`
- **Endpoint**: `POST /register` - Registrar nuevo usuario

### 5.3. ForgotPasswordController
- **Ruta**: `app/Http/Controllers/Auth/ForgotPasswordController.php`
- **Endpoints**:
  - `POST /password/email` - Solicitar restablecimiento
  - `POST /password/reset` - Restablecer contraseña

---

## 6. FrameworkController

**Ruta**: `app/Http/Controllers/FrameworkController.php`

### Descripción
Gestiona los marcos de trabajo o frameworks utilizados en los proyectos.

### Métodos Principales

#### `index()`
- **Propósito**: Listar frameworks
- **Método HTTP**: GET
- **Ruta**: `/api/frameworks`

#### `store(Request $request)`
- **Propósito**: Crear nuevo framework
- **Método HTTP**: POST
- **Ruta**: `/api/frameworks`
- **Body**:
  ```json
  {
    "name": "Nombre Framework",
    "description": "Descripción del framework",
    "version": "1.0.0"
  }
  ```

---

## 7. ProgramController

**Ruta**: `app/Http/Controllers/ProgramController.php`

### Descripción
Gestiona los programas académicos de la institución.

### Métodos Principales

#### `index()`
- **Propósito**: Listar programas
- **Método HTTP**: GET
- **Ruta**: `/api/programs`

#### `store(Request $request)`
- **Propósito**: Crear nuevo programa
- **Método HTTP**: POST
- **Ruta**: `/api/programs`
- **Body**:
  ```json
  {
    "code": "P001",
    "name": "Ingeniería de Sistemas",
    "research_group_id": 1
  }
  ```

---

## 8. ResearchGroupController

**Ruta**: `app/Http/Controllers/ResearchGroupController.php`

### Descripción
Gestiona los grupos de investigación.

### Métodos Principales

#### `index()`
- **Propósito**: Listar grupos de investigación
- **Método HTTP**: GET
- **Ruta**: `/api/research-groups`

#### `store(Request $request)`
- **Propósito**: Crear nuevo grupo
- **Método HTTP**: POST
- **Ruta**: `/api/research-groups`
- **Body**:
  ```json
  {
    "name": "Grupo de Investigación en IA",
    "initials": "GIIA",
    "description": "Grupo enfocado en IA"
  }
  ```

---

## 9. InvestigationLineController

**Ruta**: `app/Http/Controllers/InvestigationLineController.php`

### Descripción
Gestiona las líneas de investigación.

### Métodos Principales

#### `index()`
- **Propósito**: Listar líneas de investigación
- **Método HTTP**: GET
- **Ruta**: `/api/investigation-lines`

#### `store(Request $request)`
- **Propósito**: Crear nueva línea
- **Método HTTP**: POST
- **Ruta**: `/api/investigation-lines`
- **Body**:
  ```json
  {
    "name": "Inteligencia Artificial",
    "description": "Línea de IA",
    "research_group_id": 1
  }
  ```

---

## 10. BankApprovedIdeas Controllers

### 10.1. BankApprovedIdeasAssignController
- **Ruta**: `app/Http/Controllers/BankApprovedIdeasAssignController.php`
- **Propósito**: Asignar ideas aprobadas
- **Endpoints**:
  - `POST /api/bank-ideas/assign` - Asignar idea

### 10.2. BankApprovedIdeasForProfessorsController
- **Ruta**: `app/Http/Controllers/BankApprovedIdeasForProfessorsController.php`
- **Propósito**: Gestión de ideas para profesores
- **Endpoints**:
  - `GET /api/professor/bank-ideas` - Listar ideas para profesores

### 10.3. BankApprovedIdeasForStudentsController
- **Ruta**: `app/Http/Controllers/BankApprovedIdeasForStudentsController.php`
- **Propósito**: Gestión de ideas para estudiantes
- **Endpoints**:
  - `GET /api/student/bank-ideas` - Listar ideas para estudiantes

---

## 11. Otros Controladores

### CityController
- Gestión de ciudades
- **Ruta**: `app/Http/Controllers/CityController.php`

### CityProgramController
- Relación entre ciudades y programas
- **Ruta**: `app/Http/Controllers/CityProgramController.php`

### ContentFrameworkController
- Gestión de marcos de contenido
- **Ruta**: `app/Http/Controllers/ContentFrameworkController.php`

### ContentFrameworkProjectController
- Relación entre marcos de contenido y proyectos
- **Ruta**: `app/Http/Controllers/ContentFrameworkProjectController.php`

### DepartmentController
- Gestión de departamentos
- **Ruta**: `app/Http/Controllers/DepartmentController.php`

### FormularioController
- Gestión de formularios
- **Ruta**: `app/Http/Controllers/FormularioController.php`

### HomeController
- Página de inicio
- **Ruta**: `app/Http/Controllers/HomeController.php`

### PerfilController
- Gestión de perfiles de usuario
- **Ruta**: `app/Http/Controllers/PerfilController.php`

### PingController
- Verificación de estado del servidor
- **Ruta**: `app/Http/Controllers/PingController.php`

### ProjectEvaluationController
- Evaluación de proyectos
- **Ruta**: `app/Http/Controllers/ProjectEvaluationController.php`

### ThematicAreaController
- Gestión de áreas temáticas
- **Ruta**: `app/Http/Controllers/ThematicAreaController.php`

---

## Consideraciones Generales

### Autenticación
- La mayoría de los endpoints requieren autenticación mediante token JWT
- Incluir el token en el header: `Authorization: Bearer {token}`

### Validaciones
- Todos los endpoints validan los datos de entrada
- Los errores de validación devuelven código 422 con detalles

### Respuestas
- Éxito: 200 (OK), 201 (Creado), 204 (Sin contenido)
- Error: 400 (Solicitud incorrecta), 401 (No autorizado), 403 (Prohibido), 404 (No encontrado), 500 (Error interno)

### Paginación
- Los endpoints de listado soportan paginación
- Parámetros: `page`, `per_page`

---

Esta documentación proporciona una visión detallada de todos los controladores del sistema. Para información más específica sobre cada endpoint, consulte la documentación de la API o el código fuente correspondiente.
