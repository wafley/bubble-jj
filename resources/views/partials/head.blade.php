<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>@yield('title') - {{ config('meta.name') }}</title>
<meta name="Description" content="{{ config('meta.description') }}" />
<meta name="Author" content="{{ config('meta.author') }}" />
<meta name="keywords" content="{{ config('meta.keywords') }}" />

<!-- Favicon -->
<link rel="icon" href="{{ asset(config('meta.favicon')) }}" type="image/x-icon" />

<!-- Bootstrap Css -->
<link id="style" href="{{ asset('templates/libs/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" />

<!-- Style Css -->
<link href="{{ asset('templates/css/styles.css') }}" rel="stylesheet" />

<!-- Icons Css -->
<link href="{{ asset('templates/css/icons.css') }}" rel="stylesheet" />

<!-- Sweetalert Css -->
<link rel="stylesheet" href="{{ asset('templates/libs/sweetalert2/sweetalert2.min.css') }}">

<style>
    .page {
        background: linear-gradient(135deg,
                #f7f7f7 12.5%,
                #eaeaea 12.5%,
                #eaeaea 25%,
                #f7f7f7 25%,
                #eaeaea 75%,
                #f7f7f7 75%,
                #f7f7f7 87.5%,
                #eaeaea 87.5%,
                #eaeaea 100%);
        ;
        justify-content: start;
    }

    .navbar {
        position: fixed;
        top: 0;
        width: 100%;
    }

    .footer {
        position: fixed;
        bottom: 0;
        width: 100%;
        padding: 0;
        z-index: 10;
    }

    .responsive-title {
        font-size: clamp(1.8rem, 1rem + 2.5vw, 2.5rem);
        line-height: 1.1;
        font-weight: 700;
    }
</style>
