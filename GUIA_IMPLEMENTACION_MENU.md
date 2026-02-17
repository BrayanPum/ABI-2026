# 📚 GUÍA DE IMPLEMENTACIÓN - MENÚ POR ROL Y LOGO

**Proyecto:** Backend_ABI
**Fecha:** Noviembre 2025

---

## ✅ CAMBIOS REALIZADOS

### 1. Menú Organizado por Rol

Se ha actualizado completamente el menú en `config/tablar.php` con una estructura clara por roles:

#### 🎯 **Roles del Sistema**

| Rol | Descripción | Acceso |
|-----|-------------|--------|
| **research_staff** | Personal de investigación (Admin) | Acceso total al sistema |
| **committee_leader** | Líder de comité | Evaluación de proyectos + consultas |
| **professor** | Profesor | Crear/gestionar proyectos + consultas |
| **student** | Estudiante | Crear/ver proyectos + banco de ideas |

#### 📋 **Estructura del Menú**

##### SECCIÓN: INICIO (Todos)
- ✅ Panel (Dashboard)
- ✅ Perfil

##### SECCIÓN: PROYECTOS
- ✅ **Mis Proyectos** - Visible para: student, professor, committee_leader
- ✅ **Crear Proyecto** - Visible para: student, professor
- ✅ **Evaluar Proyectos** - Visible para: committee_leader
- ✅ **Banco de Ideas** - Visible para: student, professor
  - Ver Ideas (Estudiante) - solo student
  - Ver Ideas (Profesor) - solo professor

##### SECCIÓN: GESTIÓN ACADÉMICA (Solo research_staff)
- ✅ **Todos los Proyectos**
- ✅ **Estructura Académica**
  - Departamentos
  - Ciudades
  - Asignación Ciudad y Programa
  - Programas
  - Grupos de Investigación
  - Líneas de Investigación
  - Áreas Temáticas
- ✅ **Frameworks**
  - Frameworks
  - Contenidos de Framework
  - Asignación de Contenidos
- ✅ **Catálogo de Contenidos**
  - Contenidos
  - Versiones
  - Contenido por Versión

##### SECCIÓN: ADMINISTRACIÓN (Solo research_staff)
- ✅ **Usuarios**
- ✅ **Formularios**

##### SECCIÓN: CONSULTAS (Professor y Committee Leader)
- ✅ **Participantes**
- ✅ **Recursos**
  - Frameworks Disponibles
  - Guías y Documentación

---

## 🎨 SOLUCIÓN DEL LOGO EN MODO OSCURO

### Problema Original
El logo se ponía blanco/invertido en modo oscuro debido a los estilos CSS de Tablar que aplican filtros automáticos.

### Solución Implementada

#### 1. Configuración en `config/tablar.php`

```php
'logo_img' => [
    'path' => 'assets/logo.svg',
    'alt' => 'ABI Logo',
    'class' => 'logo-no-invert', // ← Clase personalizada
    'width' => 110,
    'height' => 32,
],
```

#### 2. CSS Personalizado Creado

**Archivo:** `resources/css/custom-logo.css`

Este archivo contiene estilos que previenen la inversión del logo:
- `filter: none !important` - Elimina filtros de inversión
- `opacity: 1 !important` - Mantiene opacidad completa
- Soporte para todos los temas de Tablar

#### 3. Partial Actualizado

**Archivo:** `resources/views/vendor/tablar/partials/common/logo.blade.php`

Ahora usa la configuración de `logo_img` del config, aplicando la clase `logo-no-invert`.

#### 4. Vite Config Actualizado

**Archivo:** `vite.config.js`

```javascript
input: [
    'resources/js/app.js',
    'resources/css/app.css', // ← Agregado
],
```

---

## 🚀 PASOS PARA APLICAR LOS CAMBIOS

### 1. Compilar Assets

```bash
cd C:\xampp\htdocs\Backend_ABI

# Desarrollo (con watch)
npm run dev

# O compilar para producción
npm run build
```

### 2. Limpiar Cache de Laravel

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### 3. Verificar en el Navegador

1. Abrir el proyecto en el navegador
2. Hacer login con cada rol:
   - research_staff@example.com
   - professor@example.com
   - student@example.com
   - committee_leader@example.com (si existe)
3. Verificar que cada uno ve su menú específico
4. Cambiar a modo oscuro (botón en header)
5. Verificar que el logo mantiene sus colores

---

## 🔧 PERSONALIZACIÓN ADICIONAL

### Agregar Nuevos Items al Menú

En `config/tablar.php`:

```php
[
    'text' => 'Nuevo Item',
    'icon' => 'ti ti-icon-name',
    'route' => 'ruta.nombre',
    'hasRole' => 'student', // Un solo rol
    // O
    'hasAnyRole' => ['student', 'professor'], // Múltiples roles
],
```

### Cambiar Logo

1. Subir tu logo a `public/assets/`
2. Actualizar en `config/tablar.php`:

```php
'logo_img' => [
    'path' => 'assets/tu-logo.svg', // ← Cambiar aquí
    'alt' => 'Tu Logo',
    'class' => 'logo-no-invert',
    'width' => 110,
    'height' => 32,
],
```

### Ajustar Tamaño del Logo

```php
'logo_img' => [
    // ...
    'width' => 150,  // ← Cambiar ancho
    'height' => 45,  // ← Cambiar alto
],
```

### Usar Logo Diferente para Autenticación

```php
'auth_logo' => [
    'enabled' => true,
    'img' => [
        'path' => 'assets/logo-auth.svg', // ← Logo diferente
        'alt' => 'Auth Logo',
        'class' => 'logo-no-invert',
        'width' => 150,
        'height' => 50,
    ],
],
```

---

## 🎯 ICONOS DISPONIBLES (Tabler Icons)

El proyecto usa Tabler Icons. Algunos iconos útiles:

| Categoría | Iconos |
|-----------|--------|
| **General** | `ti ti-home`, `ti ti-user`, `ti ti-settings` |
| **Proyectos** | `ti ti-book`, `ti ti-book-2`, `ti ti-books` |
| **Educación** | `ti ti-school`, `ti ti-certificate`, `ti ti-pencil` |
| **Personas** | `ti ti-users`, `ti ti-user-circle`, `ti ti-users-group` |
| **Archivos** | `ti ti-file`, `ti ti-folder`, `ti ti-files` |
| **Acciones** | `ti ti-check`, `ti ti-eye`, `ti ti-edit`, `ti ti-trash` |
| **Investigación** | `ti ti-flask`, `ti ti-git-branch`, `ti ti-bulb` |

**Ver todos:** https://tabler-icons.io/

---

## 📊 RESUMEN DE ARCHIVOS MODIFICADOS

| Archivo | Acción | Descripción |
|---------|--------|-------------|
| `config/tablar.php` | ✏️ Editado | Menú completo por rol + config logo |
| `resources/css/custom-logo.css` | ➕ Creado | CSS para prevenir inversión |
| `resources/css/app.css` | ✏️ Editado | Importa CSS personalizado |
| `resources/views/vendor/tablar/partials/common/logo.blade.php` | ✏️ Editado | Usa config de logo |
| `vite.config.js` | ✏️ Editado | Compila CSS personalizado |
| `GUIA_IMPLEMENTACION_MENU.md` | ➕ Creado | Esta guía |

---

## 🔍 TROUBLESHOOTING

### Problema: El menú no se actualiza

```bash
# Solución:
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Luego recargar el navegador con Ctrl+F5
```

### Problema: El logo sigue blanco en modo oscuro

```bash
# 1. Verificar que se compiló el CSS
npm run dev

# 2. Verificar que se cargó el CSS en el navegador
# Inspeccionar elemento > Verificar que custom-logo.css está cargado

# 3. Si no aparece, agregar manualmente en el layout:
# resources/views/vendor/tablar/master.blade.php
```

Agregar antes de `</head>`:
```blade
@vite(['resources/css/app.css'])
```

### Problema: No veo items de mi rol

**Verificar:**
1. Que el rol del usuario sea correcto en la tabla `users`
2. Que `hasRole` o `hasAnyRole` esté bien escrito
3. Que el filtro `RolePermissionMenuFilter` esté en `config/tablar.php`

```bash
# Verificar rol en tinker:
php artisan tinker
>>> Auth::user()->role
>>> Auth::user()->hasRole('student')
```

### Problema: Error "Class logo-no-invert not found"

Este no es un error real. Es solo una clase CSS que se aplica al elemento. Si el logo sigue invirtiéndose:

1. Forzar recompilación de assets:
```bash
npm run build
```

2. Verificar que el CSS se cargó:
- Inspeccionar elemento en el navegador
- Buscar "logo-no-invert" en las dev tools
- Verificar que el `filter: none !important` se aplicó

---

## 💡 MEJORAS FUTURAS

### 1. Dashboard Personalizado por Rol

Crear diferentes vistas de dashboard según el rol en `HomeController`:

```php
public function index()
{
    $user = Auth::user();

    return match($user->role) {
        'student' => view('dashboards.student'),
        'professor' => view('dashboards.professor'),
        'committee_leader' => view('dashboards.committee'),
        'research_staff' => view('dashboards.admin'),
        default => view('home'),
    };
}
```

### 2. Permisos más Granulares

Implementar Laravel Policy para permisos más específicos:

```bash
php artisan make:policy ProjectPolicy
```

### 3. Menú Dinámico desde Base de Datos

Almacenar configuración de menú en base de datos para cambios sin despliegue.

### 4. Notificaciones por Rol

Sistema de notificaciones específicas para cada rol.

### 5. Reportes por Rol

Dashboards con métricas relevantes para cada rol.

---

## 📞 SOPORTE

### Archivos de Referencia

- **Menú:** `config/tablar.php` (líneas 145-373)
- **Filtro de Roles:** `app/Filters/RolePermissionMenuFilter.php`
- **Logo:** `resources/views/vendor/tablar/partials/common/logo.blade.php`
- **CSS Logo:** `resources/css/custom-logo.css`

### Comandos Útiles

```bash
# Ver configuración actual
php artisan config:show tablar

# Limpiar todo el cache
php artisan optimize:clear

# Recompilar assets
npm run build

# Ver rutas disponibles
php artisan route:list

# Inspeccionar usuario actual
php artisan tinker
>>> Auth::user()
```

---

## ✅ CHECKLIST DE IMPLEMENTACIÓN

- [x] Actualizar `config/tablar.php` con menú por rol
- [x] Crear `resources/css/custom-logo.css`
- [x] Actualizar `resources/css/app.css`
- [x] Modificar partial del logo
- [x] Actualizar `vite.config.js`
- [x] Crear esta guía de implementación
- [ ] Compilar assets con `npm run build`
- [ ] Limpiar cache con `php artisan optimize:clear`
- [ ] Probar con cada rol
- [ ] Verificar modo oscuro
- [ ] Verificar logo mantiene colores

---

**Documento creado:** 4 de Noviembre de 2025
**Última actualización:** 4 de Noviembre de 2025
**Versión:** 1.0
