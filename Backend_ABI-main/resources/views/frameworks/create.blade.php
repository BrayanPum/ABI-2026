{{--
    View path: frameworks/create.blade.php.
    Purpose: Renders the create.blade view for the Frameworks module.
    Expected variables within this template: $message.
    No additional partials are included within this file.
    All markup below follows Tablar styling conventions for visual consistency.
--}}
@extends('layouts.app')

@section('title','Crear Marco')

@section('content')
<div class="page-body">
  <div class="container-xl">
    <div class="row justify-content-center">
      <div class="col-12 col-md-10 col-lg-8">

        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Crear nuevo Marco</h3>
          </div>
          {{-- Form element sends the captured data to the specified endpoint. --}}
          <form method="POST" action="{{ route('frameworks.store') }}">
            @csrf
            <div class="card-body">
              <div class="row g-3">

                {{-- Nombre --}}
                <div class="col-12">
                  {{-- Label describing the purpose of 'Nombre'. --}}
                  <label for="name" class="form-label required">Nombre</label>
                  {{-- Input element used to capture the 'name' value. --}}
                  <input type="text" id="name" name="name" 
                         class="form-control @error('name') is-invalid @enderror"
                         value="{{ old('name') }}" placeholder="Ej. Plan Nacional 2020–2026" required>
                  @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                {{-- Descripción --}}
                <div class="col-12">
                  {{-- Label describing the purpose of 'Descripción'. --}}
                  <label for="description" class="form-label">Descripción</label>
                  {{-- Multiline textarea allowing a detailed description for 'description'. --}}
                  <textarea id="description" name="description" 
                            class="form-control @error('description') is-invalid @enderror" 
                            rows="3" placeholder="Breve descripción">{{ old('description') }}</textarea>
                  @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                {{-- Año Inicio --}}
                <div class="col-12 col-md-6">
                  {{-- Label describing the purpose of 'Año inicio'. --}}
                  <label for="start_year" class="form-label">Año inicio</label>
                  {{-- Input element used to capture the 'start_year' value. --}}
                  <input type="number" id="start_year" name="start_year" min="1900" max="2100"
                         class="form-control @error('start_year') is-invalid @enderror"
                         value="{{ old('start_year') }}" placeholder="2020">
                  @error('start_year')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                {{-- Año Fin --}}
                <div class="col-12 col-md-6">
                  {{-- Label describing the purpose of 'Año fin'. --}}
                  <label for="end_year" class="form-label">Año fin</label>
                  {{-- Input element used to capture the 'end_year' value. --}}
                  <input type="number" id="end_year" name="end_year" min="1900" max="2100"
                         class="form-control @error('end_year') is-invalid @enderror"
                         value="{{ old('end_year') }}" placeholder="2026">
                  @error('end_year')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

              </div>
            </div>

            <div class="card-footer d-flex justify-content-end gap-2">
              <a href="{{ route('frameworks.index') }}" class="btn btn-outline-secondary">Cancelar</a>
              {{-- Button element of type 'submit' to trigger the intended action. --}}
              <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
          </form>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection
