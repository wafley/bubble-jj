@extends('layouts.auth')
@section('title', 'Masuk')

@section('content')
    <div class="row">
        <div class="col-md-6 mx-auto">
            <form action="{{ route('login') }}" method="POST" data-ajax="true">
                @csrf
                @method('POST')

                <div class="card">
                    <div class="card-header pb-0 mb-0">
                        <h3 class="fw-bold">Konfirmasi!</h3>
                        <p>Silahkan masukkan kata sandi untuk melanjutkan.</p>
                    </div>

                    <div class="card-body">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan username kamu"
                                value="{{ $prefill['username'] ?? '' }}" disabled readonly>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Nomor Whatsapp</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fe fe-phone"></i>
                                </span>
                                <input type="text" class="form-control" id="phone" name="phone" placeholder="Masukkan nomor Whatsapp kamu"
                                    value="{{ $prefill['phone'] ?? '' }}" disabled readonly>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan kata sandi kamu"
                                    value="{{ old('password') }}">
                                <button class="btn btn-light" type="button" onclick="togglePassword('password', this)">
                                    <i class='fa fa-eye'></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary d-block w-100">
                            Masuk
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
