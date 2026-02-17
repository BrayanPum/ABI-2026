<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\FormularioController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\FrameworkController;
use App\Http\Controllers\ContentFrameworkController;
use App\Http\Controllers\ContentFrameworkProjectController;
use App\Http\Controllers\InvestigationLineController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\ResearchGroupController;
use App\Http\Controllers\ThematicAreaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectEvaluationController;
use App\Http\Controllers\BankApprovedIdeasForStudentsController;
use App\Http\Controllers\BankApprovedIdeasForProfessorsController;
use App\Http\Controllers\CityProgramController;
use App\Http\Controllers\BankApprovedIdeasAssignController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/home', [HomeController::class, 'index'])->name('home');

// Auth::routes();
// Basic authentication routes (login, logout, etc.)
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Protected routes for research_staff role
Route::middleware(['auth', 'role:research_staff'])->group(function () {
    // Users
    // New user registration
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);

    Route::get('user/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::put('users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');

    // Profile (edición solo personal de investigaciones)
    Route::get('/perfil/editar', [PerfilController::class, 'edit'])->name('perfil.edit');
    Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update');
    
    //  Added routes for Departments and Cities (new addition)
    // These were added to manage departments and their related cities
    Route::resource('departments', DepartmentController::class);
    Route::resource('cities', CityController::class);
    Route::resource('city-program', CityProgramController::class);
    Route::get('obtener-ciudades-por-departamento/{id}', [CityController::class, 'obtenerCiudadesPorDepartamento']);
    Route::get('/obtener-ciudades-por-departamento/{id}', [DepartmentController::class, 'ciudadesPorDepartamento'])->name('obtener-ciudades-por-departamento');
    // End of added routes

    // Forms
    Route::resource('/formulario', FormularioController::class);

    // Academic part structure
    Route::resource('research-groups', ResearchGroupController::class);
    Route::resource('programs', ProgramController::class);
    Route::resource('investigation-lines', InvestigationLineController::class);
    Route::resource('thematic-areas', ThematicAreaController::class);

    // Catálogo de contenidos y versiones (interfaces Tablar)
    Route::view('contents', 'contents.index')->name('contents.index');

    // Other research staff resources remain available in this group.

    Route::view('versions', 'versions.index')->name('versions.index');
    Route::view('versions/create', 'versions.create')->name('versions.create');
    Route::get('versions/{versionId}/edit', function (int $versionId) {
        return view('versions.edit', ['versionId' => $versionId]);
    })->name('versions.edit');
    Route::get('versions/{versionId}', function (int $versionId) {
     return view('versions.show', ['versionId' => $versionId]);
    })->name('versions.show');

    Route::view('content-versions', 'content-versions.index')->name('content-versions.index');
    Route::view('content-versions/create', 'content-versions.create')->name('content-versions.create');
    Route::get('content-versions/{contentVersionId}/edit', function (int $contentVersionId) {
        return view('content-versions.edit', ['contentVersionId' => $contentVersionId]);
    })->name('content-versions.edit');
    Route::get('content-versions/{contentVersionId}', function (int $contentVersionId) {
        return view('content-versions.show', ['contentVersionId' => $contentVersionId]);
    })->name('content-versions.show');

    // Public routes for departments and cities (if you need them without authentication)
    Route::get('/obtener-ciudades-por-departamento/{id}', [DepartmentController::class, 'ciudadesPorDepartamento'])->name('obtener-ciudades-por-departamento');
    Route::get('/obtener-ciudades/{id_departamento}', [DepartmentController::class, 'ciudadesPorDepartamento']);
    Route::resource('/framework', App\Http\Controllers\FrameworkController::class);
    // Framework routes and content framework resources
    Route::resource('frameworks', FrameworkController::class);
    Route::resource('content-frameworks', ContentFrameworkController::class)->names('content-frameworks');
    Route::resource('content-framework-project', ContentFrameworkProjectController::class)->names('content-framework-project');
});

Route::middleware(['auth'])->group(function () {
    // Perfil (vista de solo lectura para cualquier usuario autenticado)
    Route::get('/perfil', [PerfilController::class, 'show'])->name('perfil.show');

    Route::get('projects/participants', [ProjectController::class, 'participants'])
        ->middleware('role:professor,committee_leader') // Keep the catalog restricted to professors and committee leaders.
        ->name('projects.participants');

    // Vista navegable para participantes
    Route::get('consultas/participantes', [ProjectController::class, 'participantsPage'])
        ->middleware('role:professor,committee_leader')
        ->name('participants.index');

    Route::resource('projects', ProjectController::class)->except(['destroy']);
});

Route::middleware(['auth', 'role:committee_leader'])->prefix('comite/projects/evaluation')->name('projects.evaluation.')->group(function () {
    Route::get('/', [ProjectEvaluationController::class, 'index'])->name('index');
    Route::get('/{project}', [ProjectEvaluationController::class, 'show'])->name('show');
    Route::post('/{project}/evaluate', [ProjectEvaluationController::class, 'evaluate'])->name('evaluate');
});

Route::middleware(['auth', 'role:student'])->prefix('students/projects')->group(function () {
    Route::get('approved', [BankApprovedIdeasForStudentsController::class, 'index'])
        ->name('students.projects.approved.index');

    Route::get('approved/{project}', [BankApprovedIdeasForStudentsController::class, 'show'])
        ->name('students.projects.approved.show');

    Route::get('{project}/select', [BankApprovedIdeasAssignController::class, 'select'])
        ->name('projects.student.select');

    Route::post('{project}/assign', [BankApprovedIdeasAssignController::class, 'assign'])
        ->name('projects.student.assign');
});

Route::middleware(['auth', 'role:professor'])->group(function () {
    Route::get('professor/projects/approved', [BankApprovedIdeasForProfessorsController::class, 'index'])
        ->name('professor.projects.approved.index');

    Route::get('professor/projects/approved/{project}', [BankApprovedIdeasForProfessorsController::class, 'show'])
        ->name('professor.projects.approved.show');
});

Route::middleware(['auth', 'role:committee_leader'])->group(function () {
    Route::get('professor/projects/approved', [BankApprovedIdeasForProfessorsController::class, 'index'])
        ->name('professor.projects.approved.index');

    Route::get('professor/projects/approved/{project}', [BankApprovedIdeasForProfessorsController::class, 'show'])
        ->name('professor.projects.approved.show');
});
