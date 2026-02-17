@php
    $layoutData['cssClasses'] = 'navbar navbar-vertical navbar-expand-lg';
@endphp

@section('body')
    <body>
    @auth
        <div class="page">
            <!-- Sidebar -->
            @include('tablar::partials.navbar.sidebar')
            <div class="page-wrapper">
                <!-- Page Content -->
                @yield('content')
                @include('tablar::partials.footer.bottom')
            </div>
        </div>
    @endauth
    </body>
@stop