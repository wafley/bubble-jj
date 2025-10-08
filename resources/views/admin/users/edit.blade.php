@extends('layouts.admin')
@section('title', 'Edit User')

@section('content')
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('users.update', $user->id) }}" method="POST" data-ajax="true">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ $user->name }}">
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">No. Telepon</label>
                            <input type="text" name="phone" id="phone" class="form-control" value="{{ $user->phone }}">
                        </div>

                        <div class="mb-3">
                            <label for="username_1" class="form-label">Username 1</label>
                            <input type="text" name="username_1" id="username_1" class="form-control" value="{{ $user->profile->username_1 }}">
                        </div>

                        <div class="mb-3">
                            <label for="username_2" class="form-label">Username 2</label>
                            <input type="text" name="username_2" id="username_2" class="form-control" value="{{ $user->profile->username_2 }}">
                        </div>

                        <div class="mb-3 form-check form-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                {{ old('is_active', $user->is_active) ? 'Aktif' : 'Tidak Aktif' }}
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
