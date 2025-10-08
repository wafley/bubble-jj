@extends('layouts.base')

@section('layout-content')
    <div class="page">
        @include('partials.admin.navbar')

        @include('partials.admin.sidebar')

        <!-- Start::breadcrumb -->
        <div class="d-sm-flex align-items-center page-header-breadcrumb">
            <div>
                <h4 id="breadcrumb" class="fw-medium">@yield('title')</h4>
            </div>
        </div>
        <!-- End::breadcrumb -->

        <div class="main-content app-content z-1">
            <div id="page-content" class="container-fluid">
                @yield('content')
            </div>
        </div>

        @include('partials.copyright')
    </div>

    <div class="scrollToTop">
        <a href="javascript:void(0);" class="arrow">
            <i class="las la-angle-double-up fs-20 text-fixed-white"></i>
        </a>
    </div>

    <div id="responsive-overlay"></div>
@endsection

@section('scripts')
    <!-- Defaultmenu JS -->
    <script src="{{ asset('templates/js/defaultmenu.min.js') }}" data-partial="1"></script>
@endsection
