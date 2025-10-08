<!DOCTYPE html>
<html lang="en" data-theme-mode="light" data-menu-styles="light" style="--primary-rgb: 190, 144, 48;" data-default-header-styles="light">

<head>
    @include('partials.head')
    @yield('styles')
</head>

<body>
    @include('partials.loader')

    @yield('layout-content')

    <div id="modal-container">
        @yield('modal')
    </div>

    @include('partials.footer')
    @yield('scripts')
</body>

</html>
