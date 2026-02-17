<div class="nav-item dropdown">
    <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
    <div class="d-none d-xl-block ps-2">
        <div class="mt-1 small text-muted">Proyecto ABI</div>
    </div>
</a>

    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">

        @php( $logout_url = View::getSection('logout_url') ?? config('tablar.logout_url', 'logout') )
        @php( $profile_url = View::getSection('profile_url') ?? config('tablar.profile_url', 'logout') )
        @php( $setting_url = View::getSection('setting_url') ?? config('tablar.setting_url', 'home') )

        @if (config('tablar.use_route_url', true))
            @php( $profile_url = $profile_url ? route($profile_url) : '' )
            @php( $logout_url = $logout_url ? route($logout_url) : '' )
            @php( $setting_url = $setting_url ? route($setting_url) : '' )
        @else
            @php( $profile_url = $profile_url ? url($profile_url) : '' )
            @php( $logout_url = $logout_url ? url($logout_url) : '' )
            @php( $setting_url = $setting_url ? url($setting_url) : '' )
        @endif

        <a class="dropdown-item"
           href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fa fa-fw fa-power-off text-red"></i>
            {{ __('tablar::tablar.log_out') }}
        </a>

        <form id="logout-form" action="{{ $logout_url }}" method="POST" style="display: none;">
            @if(config('tablar.logout_method'))
                {{ method_field(config('tablar.logout_method')) }}
            @endif
            {{ csrf_field() }}
        </form>

    </div>
</div>
