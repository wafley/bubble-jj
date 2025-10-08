@extends('layouts.admin')
@section('title', 'Profile')

@section('content')
    <div class="row">
        <div class="col-lg-4">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Foto Profile</div>
                </div>
                <div class="card-body">
                    @php
                        $picture = Auth::user()->profile->picture_1;
                        $src = $picture ? asset('storage/profiles/' . $picture) : asset('assets/images/default.jpg');
                    @endphp
                    <form action="{{ route('profile.picture', 1) }}" method="POST" enctype="multipart/form-data" data-ajax="true">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label for="profile_picture" class="d-block w-100 h-100">
                                <img src="{{ $src }}" alt="Profile Picture" id="profile_preview" class="img-thumbnail mb-3"
                                    style="width: 150px; height: 150px; cursor: pointer;">
                            </label>
                            <input class="form-control" type="file" id="profile_picture" name="picture" accept="image/*">
                        </div>
                        <button type="submit" class="btn btn-primary">
                            Simpan
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Informasi Pribadi</div>
                </div>
                <form action="{{ route('profile.update') }}" method="POST" data-ajax="true">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama</label>
                            <input class="form-control" type="text" id="name" name="name" value="{{ Auth::user()->name }}">
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Nomor Whatsapp</label>
                            <input class="form-control" type="text" id="phone" name="phone" value="{{ Auth::user()->phone }}">
                        </div>
                        <div class="mb-3">
                            <label for="username_1" class="form-label">Username</label>
                            <input class="form-control" type="text" id="username_1" name="username_1" value="{{ Auth::user()->profile->username_1 }}">
                        </div>
                        <div>
                            <label for="password" class="form-label">Password</label>
                            <input class="form-control" type="password" id="password" name="password"
                                placeholder="Masukkan kata sandi kamu untuk mengganti">
                            <span class="form-text text-muted">Kosongkan jika tidak ingin mengubah kata sandi</span>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script data-partial="1">
        $('#profile_picture').on('change', function() {
            let input = this;
            if (input.files && input.files[0]) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    $('#profile_preview').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        });
    </script>
@endsection
