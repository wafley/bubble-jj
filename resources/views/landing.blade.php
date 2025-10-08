@extends('layouts.app')
@section('title', 'Selamat Datang')

@section('content')
    <div class="row">
        <div class="col text-center">
            <img src="{{ asset(config('meta.logo')) }}" alt="logo" width="150">
            <h1 class="responsive-title fw-bold">Mahakarya Agency</h1>
            <p class="fst-italic">
                ~ Pencipta pertama aplikasi bubblephoto ~
            </p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-9 col-xl-6 text-center mx-auto">
            <h6 class="opacity-75">
                Tempat untuk melakukan pengajuan pembuatan Video Jedag-jedug, Silahkan daftar terlebih dulu lalu
                Login untuk pembuatan Video.
            </h6>
            <div class="my-4">
                <a href="{{ route('jeje.index') }}" class="btn btn-outline-primary me-2 spa-link">
                    Cari JJ Kamu
                </a>
                <a href="{{ route('jeje.create') }}" class="btn btn-primary spa-link">
                    <i class='fe fe-upload'></i>
                    UPLOAD JJ DISINI
                </a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col text-center">
            <b class="text-primary">CS: 0852-8153-1230</b>
        </div>
    </div>
@endsection
