@extends('layouts.admin')
@section('title', 'Edit Operator')

@section('content')
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('operators.update', $operator->id) }}" method="POST" data-ajax="true">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ $operator->name }}">
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">No. Telepon</label>
                            <input type="text" name="phone" id="phone" class="form-control" value="{{ $operator->phone }}">
                        </div>

                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" name="username" id="username" class="form-control" value="{{ $operator->profile->username_1 }}">
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Isi jika ingin mengganti password">
                                <button class="btn btn-light" type="button" onclick="togglePassword('password', this)">
                                    <i class='fa fa-eye'></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                                <button class="btn btn-light" type="button" onclick="togglePassword('password_confirmation', this)">
                                    <i class='fa fa-eye'></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3 form-check form-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                {{ old('is_active', $operator->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                {{ old('is_active', $operator->is_active) ? 'Aktif' : 'Tidak Aktif' }}
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ url()->previous() }}" class="btn btn-secondary spa-link">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
