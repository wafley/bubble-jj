@extends('layouts.auth')
@section('title', 'Masuk')

@section('content')
    <div class="row">
        <div class="col text-center">
            <img src="{{ asset(config('meta.logo')) }}" alt="logo" width="100">
            <h1 class="responsive-title fw-bold">Mahakarya Agency</h1>
            <p class="fst-italic">
                Upload video Jedag Jedug untuk tampil di semua host Mahakarya.
            </p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mx-auto">
            <form action="{{ route('login') }}" method="POST" data-ajax="true">
                @csrf
                @method('POST')

                <div class="card">
                    <div class="card-header pb-0 mb-0">
                        <blockquote class="blockquote custom-blockquote primary mb-0 text-center">
                            <h6><b>Silahkan masuk!</b> Jika belum memiliki akun, akan dibuatkan otomatis.</h6>
                            <span class="quote-icon"><i class="ri-information-line"></i></span>
                        </blockquote>
                    </div>

                    <div class="card-body">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username Tiktok</label>
                            <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan username tiktok kamu"
                                value="{{ old('username') }}">
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Nomor Whatsapp</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fe fe-phone"></i>
                                </span>
                                <input type="text" class="form-control" id="phone" name="phone" placeholder="Masukkan nomor Whatsapp kamu"
                                    value="{{ old('phone') }}">
                            </div>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input primary" type="checkbox" id="remember" name="remember">
                            <label class="form-check-label text-dark" for="remember">
                                Ingat saya
                            </label>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary d-block w-100">
                            Lanjut
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
