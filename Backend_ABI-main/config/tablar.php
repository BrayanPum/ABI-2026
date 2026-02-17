<?php
use Illuminate\Support\Facades\Auth;

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    | Here you can change the default title of your admin panel.
    |
    */

    'title' => 'ABI',
    'title_prefix' => '',
    'title_postfix' => '',
    'bottom_title' => 'ABI',
    'current_version' => 'v1.0',


    /*
    |--------------------------------------------------------------------------
    | Admin Panel Logo
    |--------------------------------------------------------------------------
    |
    | Here you can change the logo of your admin panel.
    |
    */

    'logo' => '<b>Tab</b>LAR',
    'logo_img' => [
        'path' => 'assets/tablar-logo.png',
        'alt' => 'ABI Logo',
        'class' => 'logo-no-invert', // Clase personalizada para evitar inversión
        'style' => 'filter: none !important;',
        'width' => 110,
        'height' => 32,
    ],
    'logo_img_alt' => 'Admin Logo',

    /*
    |--------------------------------------------------------------------------
    | Authentication Logo
    |--------------------------------------------------------------------------
    |
    | Here you can set up an alternative logo to use on your login and register
    | screens. When disabled, the admin panel logo will be used instead.
    |
    */

    'auth_logo' => [
        'enabled' => true,
        'img' => [
            'path' => 'assets/tablar-logo.png',
            'alt' => 'Auth Logo',
            'class' => 'logo-no-invert', // Evitar inversión en modo oscuro
            'style' => 'filter: none !important;',
            'width' => 110,
            'height' => 32,
        ],
    ],

    /*
     *
     * Default path is 'resources/views/vendor/tablar' as null. Set your custom path here If you need.
     */

    'views_path' => null,

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    | Here we change the layout of your admin panel.
    |
    | For detailed instructions you can look at the layout section here:
    |
    */

    'layout' => 'combo',
    //boxed, combo, condensed, fluid, fluid-vertical, horizontal, navbar-overlap, navbar-sticky, rtl, vertical, vertical-right, vertical-transparent

    'layout_light_sidebar' => null,
    'layout_light_topbar' => true,
    'layout_enable_top_header' => false,

    /*
    |--------------------------------------------------------------------------
    | Sticky Navbar for Top Nav
    |--------------------------------------------------------------------------
    |
    | Here you can enable/disable the sticky functionality of Top Navigation Bar.
    |
    | For detailed instructions, you can look at the Top Navigation Bar classes here:
    |
    */

    'sticky_top_nav_bar' => false,

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the admin panel.
    |
    | For detailed instructions, you can look at the admin panel classes here:
    |
    */

    'classes_body' => '',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Here we can modify the url settings of the admin panel.
    |
    | For detailed instructions, you can look at the urls section here:
    |
    */

    'use_route_url' => true,
    'dashboard_url' => 'home',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password.request',
    'password_email_url' => 'password.email',
    'profile_url' => false,
    'setting_url' => false,

    /*
    |--------------------------------------------------------------------------
    | Display Alert
    |--------------------------------------------------------------------------
    |
    | Display Alert Visibility.
    |
    */
    'display_alert' => false,

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Menú organizado por roles:
    | - research_staff: Acceso total (admin)
    | - committee_leader: Evaluación de proyectos
    | - professor: Gestión de proyectos y consulta
    | - student: Proyectos y banco de ideas
    |
    | Atributos disponibles:
    | - hasRole: 'student' | 'professor' | 'committee_leader' | 'research_staff'
    | - hasAnyRole: ['professor', 'committee_leader']
    |
    */
'menu' => [
    // =================================================================
    // SECCIÓN: INICIO (Todos los roles)
    // =================================================================
    [
        'header' => 'Inicio',
    ],
    [
        'text' => 'Panel',
        'icon' => 'ti ti-home',
        'route' => 'home',
    ],
    [
        'text' => 'Perfil',
        'icon' => 'ti ti-user-circle',
        'route' => 'perfil.show',
    ],

    // =================================================================
    // SECCIÓN: PROYECTOS
    // =================================================================
    [
        'header' => 'Proyectos',
        // Visible para todos excepto research_staff (opcional)
    ],
    [
        'text' => 'Mis Proyectos',
        'icon' => 'ti ti-book',
        'route' => 'projects.index',
        // Visible para: student, professor, committee_leader
        'hasAnyRole' => ['student', 'professor', 'committee_leader'],
    ],
    [
        'text' => 'Crear Proyecto',
        'icon' => 'ti ti-book-2',
        'route' => 'projects.create',
        // Solo student y professor pueden crear
        'hasAnyRole' => ['student', 'professor'],
    ],
    [
        'text' => 'Evaluar Proyectos',
        'icon' => 'ti ti-check',
        'route' => 'projects.evaluation.index',
        'hasRole' => 'committee_leader',
    ],
    [
        'text' => 'Banco de Ideas Aprobadas',
        'icon' => 'ti ti-bulb',
        'submenu' => [
            [
                'text' => 'Ver Ideas (Estudiante)',
                'icon' => 'ti ti-eye',
                'route' => 'students.projects.approved.index',
                'hasRole' => 'student',
            ],
            [
                'text' => 'Ver Ideas (Profesor)',
                'icon' => 'ti ti-eye',
                'route' => 'professor.projects.approved.index',
                'hasRole' => 'professor',
            ],
        ],
        'hasAnyRole' => ['student', 'professor'],
    ],

    // =================================================================
    // SECCIÓN: GESTIÓN ACADÉMICA (Research Staff)
    // =================================================================
    [
        'header' => 'Gestión Académica',
        'hasRole' => 'research_staff',
    ],
    [
        'text' => 'Todos los Proyectos',
        'icon' => 'ti ti-books',
        'route' => 'projects.index',
        'hasRole' => 'research_staff',
    ],
    [
        'text' => 'Estructura Académica',
        'icon' => 'ti ti-school',
        'hasRole' => 'research_staff',
        'submenu' => [
            [
                'text' => 'Departamentos',
                'icon' => 'ti ti-building',
                'route' => 'departments.index',
            ],
            [
                'text' => 'Ciudades',
                'icon' => 'ti ti-map-pin',
                'route' => 'cities.index',
            ],
            [
                'text' => 'Asignación Ciudad y Programa',
                'icon' => 'ti ti-map-search',
                'route' => 'city-program.index',
            ],
            [
                'text' => 'Programas',
                'icon' => 'ti ti-certificate',
                'route' => 'programs.index',
            ],
            [
                'text' => 'Grupos de Investigación',
                'icon' => 'ti ti-flask',
                'route' => 'research-groups.index',
            ],
            [
                'text' => 'Líneas de Investigación',
                'icon' => 'ti ti-git-branch',
                'route' => 'investigation-lines.index',
            ],
            [
                'text' => 'Áreas Temáticas',
                'icon' => 'ti ti-stack-2',
                'route' => 'thematic-areas.index',
            ],
        ],
    ],
    [
        'text' => 'Marcos',
        'icon' => 'ti ti-hierarchy-3',
        'hasRole' => 'research_staff',
        'submenu' => [
            [
                'text' => 'Marcos',
                'icon' => 'ti ti-square-rotated',
                'route' => 'frameworks.index',
            ],
            [
                'text' => 'Contenidos de Marcos',
                'icon' => 'ti ti-folders',
                'route' => 'content-frameworks.index',
            ],
        ],
    ],
    [
        'text' => 'Catálogo de Contenidos',
        'icon' => 'ti ti-books',
        'hasRole' => 'research_staff',
        'submenu' => [
            [
                'text' => 'Contenidos',
                'icon' => 'ti ti-book',
                'route' => 'contents.index',
            ],
            [
                'text' => 'Versiones',
                'icon' => 'ti ti-refresh',
                'route' => 'versions.index',
            ],
            [
                'text' => 'Contenido por Versión',
                'icon' => 'ti ti-link',
                'route' => 'content-versions.index',
            ],
        ],
    ],

    // =================================================================
    // SECCIÓN: USUARIOS Y FORMULARIOS (Research Staff)
    // =================================================================
    [
        'header' => 'Administración',
        'hasRole' => 'research_staff',
    ],
    [
        'text' => 'Usuarios',
        'icon' => 'ti ti-users',
        'route' => 'users.index',
        'hasRole' => 'research_staff',
    ],
    [
        'text' => 'Formularios',
        'icon' => 'ti ti-file-pencil',
        'route' => 'formulario.index',
        'hasRole' => 'research_staff',
    ],

    // =================================================================
    // SECCIÓN: CONSULTAS (Professor y Committee Leader)
    // =================================================================
    [
        'header' => 'Consultas',
        'hasAnyRole' => ['professor', 'committee_leader'],
    ],
    [
        'text' => 'Participantes',
        'icon' => 'ti ti-users-group',
        'route' => 'participants.index',
        'hasAnyRole' => ['professor', 'committee_leader'],
    ],
    // Se elimina el menú "Recursos" por no tener vistas funcionales
],


    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Here we can modify the menu filters of the admin panel.
    |
    | For detailed instructions you can look the menu filters section here:
    |
    */

    'filters'=> [
        TakiElias\Tablar\Menu\Filters\GateFilter::class,
        TakiElias\Tablar\Menu\Filters\HrefFilter::class,
        TakiElias\Tablar\Menu\Filters\SearchFilter::class,
        TakiElias\Tablar\Menu\Filters\ActiveFilter::class,
        TakiElias\Tablar\Menu\Filters\ClassesFilter::class,
        TakiElias\Tablar\Menu\Filters\LangFilter::class,
        TakiElias\Tablar\Menu\Filters\DataFilter::class,

        \App\Filters\RolePermissionMenuFilter::class
    ],



    /*
    |--------------------------------------------------------------------------
    | Vite
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Vite support.
    |
    | For detailed instructions you can look the Vite here:
    | https://laravel-vite.dev
    |
    */

    'vite' => true,

];
