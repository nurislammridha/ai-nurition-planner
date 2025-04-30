@include('layouts.header', ['pageTitle' => trim($__env->yieldContent('title'))])

<div>
    @yield('content')
</div>

@include('layouts.footer')
