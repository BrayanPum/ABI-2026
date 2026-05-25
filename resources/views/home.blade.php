{{--
    Dashboard landing page for authenticated users, providing a welcome message
    and quick access cards to frequently used areas of the system.
--}}
@extends('tablar::page')

@section('title', 'Dashboard')

@section('content')
    {{-- Header section introducing the dashboard and greeting the user. --}}
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    {{-- Subheading gives context to the title below. --}}
                    <div class="page-pretitle">
                        {{ $displayName !== '' ? 'Bienvenido, ' . $displayName : 'Bienvenido' }}
                    </div>
                    <h2 class="page-title">
                        ABI - Sistema de Gestión
                    </h2>
                    <!--Perfil-->
                </div>
            </div>
        </div>
    </div>

    {{-- Welcome card summarizing the system purpose and current user details. --}}
    <div class="row mt-4 justify-content-center">
        <div class="col-lg-8">
                <div class="card card-md">
                <div class="card-body text-center py-5">
                    <span class="avatar avatar-xl rounded-circle bg-white shadow-sm mb-4 p-0 abi-home-avatar">
                        <img
                            src="{{ $profilePhotoUrl ?: asset('udi-logo.png') }}"
                            alt="{{ $profilePhotoUrl ? 'Foto de perfil de ' . $displayName : 'Logo UDI' }}"
                            class="abi-home-avatar__image"
                        >
                    </span>
                    <h1 class="card-title mb-3">{{ $displayName !== '' ? 'Hola, ' . $displayName : 'Hola' }}</h1>
                    <p>{{ $userTypeLabel }}</p>
                    <p class="text-muted mb-4">Último acceso: <strong>{{ now()->format('d/m/Y H:i') }}</strong></p>
                    <div class="text-muted fs-5 mb-4">
                        ABI es un sistema web para la gestión de ideas de proyectos de grado. Aquí podrás realizar todo el proceso de propuesta, selección, evaluación y seguimiento general de las ideas de proyecto de grado.
                    </div>
                    <div class="d-flex flex-column flex-md-row justify-content-center gap-3">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-logout" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" />
                                    <path d="M7 12h14l-3 -3" />
                                    <path d="M18 15l3 -3" />
                                </svg>
                                Cerrar sesión
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('css')
    <style>
        .abi-home-avatar {
            width: 80px;
            height: 80px;
            overflow: hidden;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .abi-home-avatar__image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
    </style>
@endpush
