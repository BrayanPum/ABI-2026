@php
    $layoutData['cssClasses'] = 'navbar navbar-expand-md sticky-top d-print-none';
@endphp
@section('body')
    <body>
    @auth
        <div class="page">
            <!-- Top Navbar -->
            <div class="sticky-top">
                @include('tablar::partials.navbar.topbar')
            </div>
            <div class="page-wrapper">
                <!-- Page Content -->
                @yield('content')
                @include('tablar::partials.footer.bottom')
            </div>
        </div>
    @endauth
    </body>
@stop