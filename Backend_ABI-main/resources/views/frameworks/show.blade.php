{{--
    View path: frameworks/show.blade.php.
    Purpose: Renders the show.blade view for the Frameworks module.
    Expected variables within this template: $c, $framework, $message.
    No additional partials are included within this file.
    All markup below follows Tablar styling conventions for visual consistency.
--}}
@extends('layouts.app')

@section('title','Detalle Marco')

@section('content')
<div class="page-body">
  <div class="container-xl">
    <div class="mb-2">
      <a href="{{ route('frameworks.index') }}" class="btn btn-outline-secondary">← Volver</a>
    </div>

    <div class="row g-3">
      {{-- INFO DEL MARCO (SOLO LECTURA) --}}
      <div class="col-12 col-lg-6">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Información del Marco</h3>
          </div>
          <div class="card-body">
            <dl class="row">
              <dt class="col-4 text-secondary">Nombre</dt>
              <dd class="col-8 fw-medium">{{ $framework->name }}</dd>

              <dt class="col-4 text-secondary">Descripción</dt>
              <dd class="col-8">{{ $framework->description ?: '—' }}</dd>

              <dt class="col-4 text-secondary">Año inicio</dt>
              <dd class="col-8">{{ $framework->start_year ?? '—' }}</dd>

              <dt class="col-4 text-secondary">Año fin</dt>
              <dd class="col-8">{{ $framework->end_year ?? '—' }}</dd>
            </dl>
          </div>
          <div class="card-footer d-flex justify-content-end">
            <a href="{{ route('frameworks.edit',$framework) }}" class="btn btn-primary">
              Editar marco
            </a>
          </div>
        </div>
      </div>

      {{-- CONTENIDOS (crear + editar + eliminar) --}}
      <div class="col-12 col-lg-6">
        <div class="card">
          <div class="card-header flex-column align-items-start">
            <h3 class="card-title mb-0">Contenidos del Marco</h3>
            <div class="card-subtitle text-secondary mt-1">{{ $framework->name }}</div>
          </div>

          <div class="card-body">
            {{-- Agregar contenido --}}
            <form method="POST" action="{{ route('frameworks.contents.store', $framework) }}" class="mb-3">
              @csrf
              <div class="row g-2 align-items-end">
                <div class="col-12 col-md-5">
                  {{-- Label describing the purpose of 'Nombre del objetivo'. --}}
                  <label class="form-label required">Nombre del objetivo</label>
                  {{-- Input element used to capture the 'name' value. --}}
                  <input name="name" class="form-control" required>
                  @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                <div class="col-12 col-md-5">
                  {{-- Label describing the purpose of 'Descripción'. --}}
                  <label class="form-label">Descripción</label>
                  {{-- Input element used to capture the 'description' value. --}}
                  <input name="description" class="form-control">
                </div>
                <div class="col-12 col-md-2 d-flex">
                  {{-- Button element of type 'button' to trigger the intended action. --}}
                  <button class="btn btn-primary ms-auto">Agregar</button>
                </div>
              </div>
            </form>

            {{-- Tabla contenidos --}}
            @if($framework->contents->count())
              <div class="table-responsive">
                <table class="table table-vcenter table-hover card-table">
                  <thead>
                    <tr>
                      <th>Nombre</th>
                      <th>Descripción</th>
                      <th class="w-25">Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($framework->contents as $c)
                      <tr>
                        <td class="text-reset">{{ $c->name }}</td>
                        <td>{{ $c->description !== '' ? $c->description : '—' }}</td>
                        <td>
                          <div class="d-flex gap-2">
                            {{-- Editar contenido: abre modal y precarga valores --}}
                            <button
                              class="btn btn-outline-primary"
                              data-bs-toggle="modal"
                              data-bs-target="#modal-edit-content"
                              data-id="{{ $c->id }}"
                              data-name="{{ $c->name }}"
                              data-description="{{ $c->description }}"
                            >Editar</button>

                            {{-- Eliminar --}}
                            <form method="POST" action="{{ route('contents.destroy',$c) }}"
                                  onsubmit="return confirm('¿Eliminar contenido?')" class="d-inline">
                              @csrf @method('DELETE')
                              {{-- Button element of type 'button' to trigger the intended action. --}}
                              <button class="btn btn-outline-danger">Eliminar</button>
                            </form>
                          </div>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @else
              <div class="text-secondary">Este marco aún no tiene contenidos.</div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- MODAL: editar contenido --}}
<div class="modal modal-blur fade" id="modal-edit-content" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document"><div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title">Editar contenido</h5>
      {{-- Button element of type 'button' to trigger the intended action. --}}
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
    </div>
    {{-- Form element sends the captured data to the specified endpoint. --}}
    <form id="form-edit-content" method="POST">
      @csrf @method('PUT')
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-12">
            {{-- Label describing the purpose of 'Nombre'. --}}
            <label class="form-label required">Nombre</label>
            {{-- Input element used to capture the 'name' value. --}}
            <input id="editc-name" name="name" class="form-control" required>
          </div>
          <div class="col-12">
            {{-- Label describing the purpose of 'Descripción'. --}}
            <label class="form-label">Descripción</label>
            {{-- Input element used to capture the 'description' value. --}}
            <input id="editc-description" name="description" class="form-control">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        {{-- Button element of type 'button' to trigger the intended action. --}}
        <button class="btn btn-link link-secondary" data-bs-dismiss="modal">Cancelar</button>
        {{-- Button element of type 'button' to trigger the intended action. --}}
        <button class="btn btn-primary">Guardar cambios</button>
      </div>
    </form>
  </div></div>
</div>

@push('scripts')
<script>
  const modal = document.getElementById('modal-edit-content');
  modal?.addEventListener('show.bs.modal', (e) => {
    const btn = e.relatedTarget;
    const id  = btn.getAttribute('data-id');
    const nm  = btn.getAttribute('data-name') || '';
    const ds  = btn.getAttribute('data-description') || '';

    document.getElementById('editc-name').value = nm;
    document.getElementById('editc-description').value = ds;

    // Ruta shallow: contents.update => /contents/{id}
    document.getElementById('form-edit-content').action = `{{ url('/contents') }}/${id}`;
  });
</script>
@endpush
@endsection
