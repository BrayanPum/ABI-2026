{{-- resources/views/layouts/app.blade.php --}}
{{--
  Layout template that establishes the global HTML structure, loads shared assets,
  and exposes sections that child views can extend. Every view that extends this
  file inherits the navigation bar, flash messages, and reusable confirmation
  logic defined below.
--}}
<!doctype html>
<html lang="es">
<head>
  {{--
    Meta information shared across the application, including charset, viewport
    for responsiveness, and the CSRF token so forms can authenticate correctly.
  --}}
  <meta charset="utf-8">
  <title>@yield('title','ABI')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  {{--
    Primary stylesheet served from the Tabler CDN to keep the UI consistent
    across all pages without bundling the library locally.
  --}}
  <link href="https://unpkg.com/@tabler/core@1.0.0-beta19/dist/css/tabler.min.css" rel="stylesheet"/>

  {{--
    Vite pipeline that compiles the local CSS and JavaScript for frontend
    behaviors specific to the application. The array ensures both assets are
    loaded together.
  --}}
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body>
  {{--
    Main page container wraps all content so the Tabler layout styles can apply
    consistently to nested components.
  --}}
  <div class="page">
    {{--
      Fixed navigation bar with branding that links back to the framework index
      route, serving as the default landing area after authentication.
    --}}
    <header class="navbar navbar-expand-md d-print-none">
      <div class="container-xl">
        <a class="navbar-brand" href="{{ route('frameworks.index') }}">
          <span class="navbar-brand-text">ABI</span>
        </a>
      </div>
    </header>

    {{--
      Wrapper that contains the page body and yields the dynamic content section
      provided by each child view. Flash messages are surfaced here so they are
      visible regardless of the page being rendered.
    --}}
    <div class="page-wrapper">
      <div class="page-body">
        <div class="container-xl">
          @if(session('ok'))
            {{-- Success alert displayed when the session contains a confirmation message. --}}
            <div class="alert alert-success">{{ session('ok') }}</div>
          @endif
          {{-- Area where child templates inject their specific markup. --}}
          @yield('content')
        </div>
      </div>
    </div>
  </div>

  {{-- JavaScript bundle from Tabler to enable the interactive components provided by the framework. --}}
  <script src="https://unpkg.com/@tabler/core@1.0.0-beta19/dist/js/tabler.min.js"></script>

  {{--
    Reusable double-confirmation mechanism that adds a second confirmation
    button next to destructive actions to prevent accidental submissions.
  --}}
  <script>
    // Listen for clicks on any element decorated with the data-confirm attribute.
    document.addEventListener('click', (e) => {
      const b = e.target.closest('[data-confirm]');
      if(!b) return;

      // Stop the default action so the confirmation layer can be injected first.
      e.preventDefault();
      const wrap = b.parentElement;

      // Avoid rendering multiple confirmation buttons if one already exists.
      let confirmBtn = wrap.querySelector('.btn-confirm');
      if (confirmBtn) return;

      // Create the confirmation button that requires a second click before executing.
      confirmBtn = document.createElement('button');
      confirmBtn.className = 'btn btn-danger btn-confirm';
      confirmBtn.style.opacity = .35;
      confirmBtn.textContent = 'Confirmar';
      wrap.appendChild(confirmBtn);

      // Delay arming the confirmation button to avoid accidental double clicks.
      setTimeout(() => { confirmBtn.style.opacity = 1; confirmBtn.dataset.armed = '1'; }, 1200);

      // Once armed, clicking the confirmation button submits the referenced form.
      confirmBtn.addEventListener('click', () => {
        if (!confirmBtn.dataset.armed) return;
        const formId = b.dataset.action;
        document.querySelector(formId)?.submit();
      });

      // Provide a cancel button so the user can back out of the confirmation state.
      const cancel = document.createElement('button');
      cancel.className = 'btn btn-link text-secondary';
      cancel.textContent = 'Cancelar';
      cancel.addEventListener('click', () => { confirmBtn.remove(); cancel.remove(); });
      wrap.appendChild(cancel);
    });
  </script>

  {{-- Placeholder stack so individual views can push additional scripts when required. --}}
  @stack('scripts')
</body>
</html>
