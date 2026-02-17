<header class="navbar navbar-expand-md d-none d-lg-flex d-print-none">
  <div class="container-xl">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Navigation menu -->
    <div class="collapse navbar-collapse" id="navbar-menu">
      <!-- Left side -->
      <ul class="navbar-nav me-auto">
        <!-- Add your menu items here -->
      </ul>

      <!-- Right side -->
      <ul class="navbar-nav ms-auto">
        @auth
          <!-- Authenticated user menu -->
          <li class="nav-item">
            <a href="#" class="nav-link d-flex lh-1 text-reset p-0">
              <span class="fw-bold fs-4">Bienvenid@: {{ auth()->user()->name }}</span>
              <br>
           <!--    <span class="fw-bold fs-4">{{ auth()->user()->email }}</span> -->
            </a>
          </li>
        @else
          <!-- Guest menu -->
          <li class="nav-item">
            <a href="{{ route('login') }}" class="nav-link">Iniciar sesión</a>
          </li>
        @endauth

        <!-- Theme mode and top-right partials -->
        <li class="nav-item d-none d-md-flex">
          @include('tablar::partials.header.theme-mode')
        </li>
        <li class="nav-item">
          @include('tablar::partials.header.top-right')
        </li>
      </ul>
    </div>
  </div>
</header>