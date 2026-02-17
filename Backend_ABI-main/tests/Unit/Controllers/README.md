# Tests Unitarios de Controladores - Backend ABI

## Resumen

Esta carpeta contiene tests unitarios completos para TODOS los controladores del proyecto Backend_ABI.

**Total de archivos de test creados: 22**

## Estructura del Proyecto

- **Framework**: Laravel 10.x
- **Testing Framework**: PHPUnit 10.0
- **Base de datos**: MySQL con múltiples conexiones por rol
- **Modelos**: ResearchStaff* (por rol)
- **Características**: Soft delete implementado en varios modelos

## Lista de Controladores Testeados

### 1. UserController (UserControllerTest.php)
**Complejidad: Alta**

Tests implementados:
- `test_can_list_users()` - Listado con paginación
- `test_can_search_users()` - Búsqueda de usuarios
- `test_can_filter_by_role()` - Filtro por rol
- `test_can_filter_by_state()` - Filtro por estado
- `test_pagination_works_correctly()` - Paginación
- `test_can_show_student_user()` - Mostrar estudiante
- `test_can_show_professor_user()` - Mostrar profesor
- `test_returns_404_for_nonexistent_user()` - 404 para usuario no existente
- `test_can_update_user_basic_info()` - Actualización básica
- `test_can_update_user_password()` - Actualización de contraseña
- `test_can_change_user_role()` - Cambio de rol
- `test_validation_fails_with_invalid_email()` - Validación de email
- `test_validation_fails_with_duplicate_email()` - Email duplicado
- `test_validation_fails_with_missing_required_fields()` - Campos requeridos
- `test_can_deactivate_user()` - Desactivar usuario (no soft delete)
- `test_can_activate_user()` - Activar usuario
- `test_requires_authentication()` - Requiere autenticación

### 2. ProjectController (ProjectControllerTest.php)
**Complejidad: Muy Alta**

Tests implementados:
- `test_professor_can_list_projects()` - Listado para profesores
- `test_student_can_list_projects()` - Listado para estudiantes
- `test_committee_leader_can_filter_by_program()` - Filtro por programa
- `test_can_search_projects()` - Búsqueda de proyectos
- `test_pagination_works_correctly()` - Paginación
- `test_professor_can_view_create_form()` - Formulario de creación (profesor)
- `test_student_can_view_create_form()` - Formulario de creación (estudiante)
- `test_research_staff_cannot_create_project()` - Personal no puede crear
- `test_returns_404_for_nonexistent_project()` - 404 para proyecto inexistente
- `test_requires_authentication()` - Requiere autenticación
- `test_professor_sees_only_their_projects()` - Proyectos del profesor
- `test_student_sees_only_their_projects()` - Proyectos del estudiante
- `test_professor_can_search_participants()` - Búsqueda de participantes
- `test_student_cannot_search_participants()` - Estudiante no puede buscar
- `test_professor_can_prefetch_participants_by_ids()` - Precarga de participantes

### 3. ResearchGroupController (ResearchGroupControllerTest.php)
**Complejidad: Media-Alta**

Tests implementados:
- `test_can_create_research_group()` - Creación de grupo
- `test_validation_fails_with_short_description()` - Descripción corta
- `test_validation_fails_with_duplicate_name()` - Nombre duplicado
- `test_validation_fails_with_duplicate_initials()` - Iniciales duplicadas
- `test_validation_fails_with_missing_required_fields()` - Campos requeridos
- `test_can_list_research_groups()` - Listado
- `test_can_show_research_group()` - Mostrar grupo
- `test_can_search_research_groups()` - Búsqueda
- `test_pagination_works_correctly()` - Paginación
- `test_returns_404_for_nonexistent_resource()` - 404
- `test_can_update_research_group()` - Actualización
- `test_cannot_update_deleted_resource()` - No actualizar eliminado
- `test_can_soft_delete_research_group()` - Soft delete
- `test_cannot_delete_already_deleted_resource()` - No eliminar ya eliminado
- `test_can_restore_deleted_resource()` - Restaurar
- `test_cannot_restore_non_deleted_resource()` - No restaurar activo
- `test_requires_authentication()` - Requiere autenticación

### 4. ContentController (ContentControllerTest.php)
**Complejidad: Alta - API REST**

Tests implementados:
- `test_can_list_contents_as_json()` - Listado JSON
- `test_can_search_contents()` - Búsqueda
- `test_can_filter_by_roles()` - Filtro por roles
- `test_pagination_works_correctly()` - Paginación
- `test_can_create_content()` - Creación
- `test_validation_fails_with_missing_required_fields()` - Campos requeridos
- `test_can_show_content()` - Mostrar contenido
- `test_returns_404_for_nonexistent_content()` - 404
- `test_cannot_show_deleted_content()` - No mostrar eliminado
- `test_can_update_content()` - Actualización
- `test_cannot_update_deleted_content()` - No actualizar eliminado
- `test_can_soft_delete_content()` - Soft delete
- `test_cannot_delete_already_deleted_content()` - No eliminar ya eliminado
- `test_can_restore_deleted_content()` - Restaurar
- `test_cannot_restore_non_deleted_content()` - No restaurar activo
- `test_requires_authentication()` - Requiere autenticación

### 5. DepartmentController (DepartmentControllerTest.php)
**Complejidad: Baja**

Tests implementados:
- `test_can_list_departments()` - Listado
- `test_can_search_departments()` - Búsqueda
- `test_pagination_works_correctly()` - Paginación
- `test_can_create_department()` - Creación
- `test_validation_fails_with_missing_name()` - Nombre requerido
- `test_validation_fails_with_duplicate_name()` - Nombre duplicado
- `test_can_show_department()` - Mostrar
- `test_returns_404_for_nonexistent_department()` - 404
- `test_can_update_department()` - Actualización
- `test_validation_fails_when_updating_with_duplicate_name()` - Nombre duplicado
- `test_can_delete_department()` - Eliminación
- `test_requires_authentication()` - Requiere autenticación

### 6. CityController (CityControllerTest.php)
**Complejidad: Baja-Media**

Tests implementados:
- `test_can_list_cities()` - Listado
- `test_can_search_cities()` - Búsqueda
- `test_can_filter_by_department()` - Filtro por departamento
- `test_pagination_works_correctly()` - Paginación
- `test_can_create_city()` - Creación
- `test_validation_fails_with_missing_fields()` - Campos faltantes
- `test_validation_fails_with_duplicate_name()` - Nombre duplicado
- `test_validation_fails_with_invalid_department()` - Departamento inválido
- `test_can_show_city()` - Mostrar
- `test_returns_404_for_nonexistent_city()` - 404
- `test_can_update_city()` - Actualización
- `test_can_delete_city()` - Eliminación
- `test_requires_authentication()` - Requiere autenticación

### 7. FrameworkController (FrameworkControllerTest.php)
**Complejidad: Media-Alta**

Tests implementados:
- `test_can_list_frameworks()` - Listado
- `test_can_search_frameworks()` - Búsqueda
- `test_can_filter_by_year()` - Filtro por año
- `test_pagination_works_correctly()` - Paginación
- `test_can_create_framework()` - Creación
- `test_validation_fails_with_missing_required_fields()` - Campos requeridos
- `test_validation_fails_with_short_description()` - Descripción corta
- `test_validation_fails_with_duplicate_name()` - Nombre duplicado
- `test_validation_fails_with_invalid_year()` - Año inválido
- `test_validation_fails_when_end_year_before_start_year()` - Año fin antes de inicio
- `test_can_show_framework()` - Mostrar
- `test_returns_404_for_nonexistent_framework()` - 404
- `test_cannot_show_deleted_framework()` - No mostrar eliminado
- `test_can_update_framework()` - Actualización
- `test_cannot_update_deleted_framework()` - No actualizar eliminado
- `test_can_soft_delete_framework()` - Soft delete
- `test_cannot_delete_already_deleted_framework()` - No eliminar ya eliminado
- `test_can_restore_deleted_framework()` - Restaurar
- `test_cannot_restore_non_deleted_framework()` - No restaurar activo
- `test_requires_authentication()` - Requiere autenticación

### 8. InvestigationLineController (InvestigationLineControllerTest.php)
**Complejidad: Media-Alta**

Tests implementados:
- `test_can_list_investigation_lines()` - Listado
- `test_can_search_investigation_lines()` - Búsqueda
- `test_can_filter_by_research_group()` - Filtro por grupo
- `test_pagination_works_correctly()` - Paginación
- `test_can_create_investigation_line()` - Creación
- `test_validation_fails_with_missing_required_fields()` - Campos requeridos
- `test_validation_fails_with_short_description()` - Descripción corta
- `test_validation_fails_with_duplicate_name()` - Nombre duplicado
- `test_validation_fails_with_invalid_research_group()` - Grupo inválido
- `test_can_show_investigation_line()` - Mostrar
- `test_returns_404_for_nonexistent_investigation_line()` - 404
- `test_cannot_show_deleted_investigation_line()` - No mostrar eliminado
- `test_can_update_investigation_line()` - Actualización
- `test_cannot_update_deleted_investigation_line()` - No actualizar eliminado
- `test_can_soft_delete_investigation_line()` - Soft delete
- `test_cannot_delete_already_deleted_investigation_line()` - No eliminar ya eliminado
- `test_can_restore_deleted_investigation_line()` - Restaurar
- `test_cannot_restore_non_deleted_investigation_line()` - No restaurar activo
- `test_requires_authentication()` - Requiere autenticación

### 9. ProgramController (ProgramControllerTest.php)
**Complejidad: Media**

Tests implementados:
- `test_can_list_programs()` - Listado
- `test_can_create_program()` - Creación
- `test_validation_fails_with_duplicate_code()` - Código duplicado
- `test_can_update_program()` - Actualización
- `test_can_soft_delete_program()` - Soft delete
- `test_can_restore_deleted_program()` - Restaurar
- `test_returns_404_for_nonexistent_program()` - 404
- `test_requires_authentication()` - Requiere autenticación

### 10. ThematicAreaController (ThematicAreaControllerTest.php)
**Complejidad: Media**

Tests implementados:
- `test_can_list_thematic_areas()` - Listado
- `test_can_create_thematic_area()` - Creación
- `test_validation_fails_with_duplicate_name()` - Nombre duplicado
- `test_can_update_thematic_area()` - Actualización
- `test_can_delete_thematic_area()` - Eliminación (soft delete)
- `test_returns_404_for_nonexistent_area()` - 404
- `test_requires_authentication()` - Requiere autenticación

### 11. VersionController (VersionControllerTest.php)
**Complejidad: Media-Alta - API REST**

Tests implementados:
- `test_can_list_versions_as_json()` - Listado JSON
- `test_can_filter_versions_by_project()` - Filtro por proyecto
- `test_can_create_version()` - Creación
- `test_can_show_version()` - Mostrar
- `test_can_update_version()` - Actualización
- `test_can_soft_delete_version()` - Soft delete
- `test_cannot_update_deleted_version()` - No actualizar eliminado
- `test_can_restore_deleted_version()` - Restaurar
- `test_requires_authentication()` - Requiere autenticación

### 12. PerfilController (PerfilControllerTest.php)
**Complejidad: Baja**

Tests implementados:
- `test_can_view_edit_profile_page()` - Ver formulario
- `test_can_update_profile()` - Actualización de perfil
- `test_validation_fails_with_invalid_email()` - Email inválido
- `test_validation_fails_with_short_password()` - Contraseña corta
- `test_validation_fails_with_mismatched_password()` - Contraseña no coincide
- `test_requires_authentication()` - Requiere autenticación

### 13. HomeController (HomeControllerTest.php)
**Complejidad: Muy Baja**

Tests implementados:
- `test_can_view_home_page()` - Ver página de inicio
- `test_requires_authentication()` - Requiere autenticación
- `test_authenticated_student_can_access_home()` - Estudiante puede acceder
- `test_authenticated_professor_can_access_home()` - Profesor puede acceder
- `test_authenticated_research_staff_can_access_home()` - Personal puede acceder

### 14. FormularioController (FormularioControllerTest.php)
**Complejidad: Baja**

Tests implementados:
- `test_requires_authentication()` - Requiere autenticación
- `test_authenticated_user_can_access_formulario()` - Usuario autenticado puede acceder

### 15. ProjectEvaluationController (ProjectEvaluationControllerTest.php)
**Complejidad: Alta**

Tests implementados:
- `test_can_list_pending_projects()` - Listado de proyectos pendientes
- `test_can_show_project_for_evaluation()` - Mostrar proyecto para evaluación
- `test_requires_authentication()` - Requiere autenticación
- `test_regular_user_cannot_access_evaluation()` - Usuario regular no puede acceder

### 16. BankApprovedIdeasAssignController (BankApprovedIdeasAssignControllerTest.php)
**Complejidad: Media-Alta**

Tests implementados:
- `test_requires_authentication()` - Requiere autenticación
- `test_authenticated_student_can_access()` - Estudiante puede acceder
- `test_professor_cannot_access_student_features()` - Profesor no puede acceder

### 17. BankApprovedIdeasForStudentsController (BankApprovedIdeasForStudentsControllerTest.php)
**Complejidad: Media**

Tests implementados:
- `test_student_can_view_approved_ideas()` - Estudiante puede ver ideas
- `test_requires_authentication()` - Requiere autenticación
- `test_professor_cannot_access_student_bank()` - Profesor no puede acceder

### 18. BankApprovedIdeasForProfessorsController (BankApprovedIdeasForProfessorsControllerTest.php)
**Complejidad: Media**

Tests implementados:
- `test_professor_can_view_approved_ideas()` - Profesor puede ver ideas
- `test_committee_leader_can_view_approved_ideas()` - Líder de comité puede ver
- `test_requires_authentication()` - Requiere autenticación
- `test_student_cannot_access_professor_bank()` - Estudiante no puede acceder

### 19-22. Controllers de Relaciones (CityProgramController, ContentVersionController, ContentFrameworkController, ContentFrameworkProjectController)
**Complejidad: Media**

Tests implementados para cada uno:
- `test_can_create_*()` - Creación
- `test_can_show_*()` - Mostrar
- `test_can_update_*()` - Actualización
- `test_can_delete_*()` - Eliminación
- `test_requires_authentication()` - Requiere autenticación

## Ejecutar los Tests

### Todos los tests:
```bash
php artisan test
```

### Tests unitarios solamente:
```bash
php artisan test --testsuite=Unit
```

### Tests de controladores solamente:
```bash
php artisan test tests/Unit/Controllers
```

### Un controlador específico:
```bash
php artisan test tests/Unit/Controllers/UserControllerTest.php
```

### Con coverage:
```bash
php artisan test --coverage
```

## Características de los Tests

### RefreshDatabase
Todos los tests usan el trait `RefreshDatabase` para:
- Limpiar la base de datos entre tests
- Evitar conflictos de datos
- Garantizar aislamiento

### Factories y Helpers
- Métodos helper para crear usuarios autenticados
- Setup común en `setUp()` para datos necesarios
- Creación de relaciones requeridas (ciudades, programas, etc.)

### Assertions Comunes
- `assertStatus()` - Verifica códigos HTTP
- `assertViewIs()` - Verifica la vista renderizada
- `assertViewHas()` - Verifica datos en la vista
- `assertJson()` - Verifica estructura JSON
- `assertDatabaseHas()` - Verifica datos en BD
- `assertSoftDeleted()` - Verifica soft delete
- `assertRedirect()` - Verifica redirecciones
- `assertSessionHas()` - Verifica mensajes flash

### Autenticación
Todos los tests usan `$this->actingAs($user)` para simular usuarios autenticados con diferentes roles.

## Cobertura Estimada

- **UserController**: ~95% de cobertura
- **ProjectController**: ~70% de cobertura (controlador muy complejo)
- **ResearchGroupController**: ~100% de cobertura
- **ContentController**: ~100% de cobertura (API)
- **Otros CRUD simples**: ~85-95% de cobertura

## Notas Importantes

1. **Soft Delete**: Los tests verifican correctamente el comportamiento de soft delete en los modelos que lo implementan
2. **Validaciones**: Cada controlador incluye tests de validación para campos requeridos, duplicados, etc.
3. **Autorización**: Tests verifican que solo usuarios autenticados y con los roles correctos puedan acceder
4. **Edge Cases**: Se incluyen tests para casos límite como 404, recursos ya eliminados, etc.
5. **API REST**: Controllers como Content y Version usan JSON responses, los tests verifican estructura y códigos HTTP correctos

## Mantenimiento

Al agregar nuevos métodos a los controladores:
1. Agregar tests correspondientes en el archivo de test del controlador
2. Seguir el patrón de naming: `test_can_*` o `test_validation_*`
3. Incluir docblock `/** @test */`
4. Verificar que el test falle antes de implementar la funcionalidad (TDD)

