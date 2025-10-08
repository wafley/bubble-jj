@extends('layouts.base')

@section('layout-content')
    <div class="page page-h">
        <div id="page-content" class="container m-auto">
            @yield('content')
        </div>

        @include('partials.copyright')
    </div>
@endsection
