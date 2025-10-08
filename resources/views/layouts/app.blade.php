@extends('layouts.base')

@section('layout-content')
    <div class="page">
        @include('partials.navbar')

        <div class="main-content">
            <div id="page-content" class="container">
                @yield('content')
            </div>
        </div>

        @include('partials.copyright')
    </div>
@endsection
