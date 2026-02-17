<a href="{{ config('tablar.dashboard_url', 'home') }}">
    @if(config('tablar.logo_img.path'))
        <img src="{{ asset(config('tablar.logo_img.path', 'assets/logo.svg')) }}"
             width="{{ config('tablar.logo_img.width', 110) }}"
             height="{{ config('tablar.logo_img.height', 32) }}"
             alt="{{ config('tablar.logo_img.alt', 'ABI Logo') }}"
             class="navbar-brand-image {{ config('tablar.logo_img.class', '') }}"
             @if(config('tablar.logo_img.style')) style="{{ config('tablar.logo_img.style') }}" @endif>
    @else
        {!! config('tablar.logo', '<b>Tab</b>LAR') !!}
    @endif
</a>
