@extends('layouts.app')
@section('title', 'Profile')

@section('content')
    <div class="d-flex align-items-center mb-3">
        <a href="{{ route('home') }}" class="btn btn-sm d-flex align-items-center spa-link">
            <i class="bi bi-arrow-left-short fs-3"></i>
            <span class="fw-bold fs-5">Kembali</span>
        </a>
    </div>

    <div class="row">
        <div class="col-lg-7">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Edit Profile</div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col">
                            <div class="d-flex gap-2 justify-content-center">
                                @for ($i = 1; $i <= 4; $i++)
                                    @php
                                        $picture = Auth::user()->profile->{'picture_' . $i};
                                        $src = $picture ? asset('storage/profiles/' . $picture) : asset('assets/images/upload-placeholder.png');
                                    @endphp

                                    <label for="picture_{{ $i }}">
                                        <img src="{{ $src }}" class="img-thumbnail"
                                            style="height: 100px; width: 65px; object-fit: contain; cursor: pointer;">
                                    </label>

                                    <input type="file" name="picture" id="picture_{{ $i }}" data-url="{{ route('profile.picture', $i) }}"
                                        accept="image/*" hidden>
                                @endfor
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('profile.update') }}" method="POST" data-ajax="true">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama</label>
                            <input type="text" class="form-control form-control-sm" name="name" id="name" placeholder="Masukkan nama kamu"
                                value="{{ Auth::user()->name ?? '' }}">
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Nomor Whatsapp</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fe fe-phone"></i>
                                </span>
                                <input type="text" class="form-control form-control-sm" name="phone" id="phone"
                                    placeholder="Masukkan nomor Whatsapp kamu" value="{{ Auth::user()->phone }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="username_1" class="form-label">Username Tiktok (Utama)</label>
                            <input type="text" class="form-control form-control-sm" name="username_1" id="username_1"
                                placeholder="Masukkan akun tiktok utama kamu" value="{{ Auth::user()->profile->username_1 }}">
                        </div>

                        <div>
                            <label for="username_2" class="form-label">Username Tiktok Kedua</label>
                            <input type="text" class="form-control form-control-sm" name="username_2" id="username_2"
                                placeholder="Masukkan Username tiktok kedua (Opsional)" value="{{ Auth::user()->profile->username_2 }}">
                        </div>

                        <hr />

                        <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Video JJ Kamu</div>
                </div>
                <div class="card-body">
                    <div class="accordion" id="videos">
                        <div class="accordion-item">
                            <div class="d-flex gap-2 align-items-center">
                                <h2 class="accordion-header flex-1">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#display-10"
                                        aria-expanded="false" aria-controls="display-10">
                                        10 detik
                                    </button>
                                </h2>
                                @php
                                    $url10 = !empty($videos[10] ?? []) ? route('jeje.destroy', $videos[10][0]->id) : 'javascript:void(0)';
                                    $disabled = empty($videos[10] ?? []);
                                @endphp
                                <a href="{{ $url10 }}" class="btn btn-sm btn-primary me-2 spa-link {{ $disabled ? 'disabled' : '' }}">
                                    Detail
                                    <i class="bi bi-arrow-right text-white"></i>
                                </a>
                            </div>
                            <div id="display-10" class="accordion-collapse collapse" data-bs-parent="#videos">
                                <div class="accordion-body">
                                    @forelse ($videos[10] ?? [] as $video)
                                        <div class="card">
                                            <video src="{{ asset('storage/jj/' . $video->filename) }}" class="card-img-top" controls></video>
                                            <div class="card-body">
                                                <p class="card-text">Durasi: {{ $video->duration }} detik | Size:
                                                    {{ formatSize($video->size) }}
                                                </p>
                                                <p class="card-text">
                                                    <small class="text-body-secondary">
                                                        {{ formatDate($video->updated_at) }}
                                                    </small>
                                                </p>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-muted">Belum ada video untuk jenis ini.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <div class="d-flex gap-2 align-items-center">
                                <h2 class="accordion-header flex-1">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#display-20"
                                        aria-expanded="false" aria-controls="display-20">
                                        15 detik
                                    </button>
                                </h2>
                                @php
                                    $url20 = !empty($videos[20] ?? []) ? route('jeje.destroy', $videos[20][0]->id) : 'javascript:void(0)';
                                    $disabled = empty($videos[20] ?? []);
                                @endphp
                                <a href="{{ $url20 }}" class="btn btn-sm btn-primary me-2 spa-link {{ $disabled ? 'disabled' : '' }}">
                                    Detail
                                    <i class="bi bi-arrow-right text-white"></i>
                                </a>
                            </div>
                            <div id="display-20" class="accordion-collapse collapse" data-bs-parent="#videos">
                                <div class="accordion-body">
                                    @forelse ($videos[20] ?? [] as $video)
                                        <div class="card">
                                            <video src="{{ asset('storage/jj/' . $video->filename) }}" class="card-img-top" controls></video>
                                            <div class="card-body">
                                                <p class="card-text">Durasi: {{ $video->duration }} detik | Size:
                                                    {{ formatSize($video->size) }}
                                                </p>
                                                <p class="card-text">
                                                    <small class="text-body-secondary">
                                                        {{ formatDate($video->updated_at) }}
                                                    </small>
                                                </p>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-muted">Belum ada video untuk jenis ini.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <div class="d-flex gap-2 align-items-center">
                                <h2 class="accordion-header flex-1">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#display-30"
                                        aria-expanded="false" aria-controls="display-30">
                                        25 detik
                                    </button>
                                </h2>
                                @php
                                    $url30 = !empty($videos[30] ?? []) ? route('jeje.destroy', $videos[30][0]->id) : 'javascript:void(0)';
                                    $disabled = empty($videos[30] ?? []);
                                @endphp
                                <a href="{{ $url30 }}" class="btn btn-sm btn-primary me-2 spa-link {{ $disabled ? 'disabled' : '' }}">
                                    Detail
                                    <i class="bi bi-arrow-right text-white"></i>
                                </a>
                            </div>
                            <div id="display-30" class="accordion-collapse collapse" data-bs-parent="#videos">
                                <div class="accordion-body">
                                    @forelse ($videos[30] ?? [] as $video)
                                        <div class="card">
                                            <video src="{{ asset('storage/jj/' . $video->filename) }}" class="card-img-top" controls></video>
                                            <div class="card-body">
                                                <p class="card-text">Durasi: {{ $video->duration }} detik | Size:
                                                    {{ formatSize($video->size) }}
                                                </p>
                                                <p class="card-text">
                                                    <small class="text-body-secondary">
                                                        {{ formatDate($video->updated_at) }}
                                                    </small>
                                                </p>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-muted">Belum ada video untuk jenis ini.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <div class="d-flex gap-2 align-items-center">
                                <h2 class="accordion-header flex-1">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#display-99"
                                        aria-expanded="false" aria-controls="display-99">
                                        60 detik
                                    </button>
                                </h2>
                                @php
                                    $url99 = !empty($videos[99] ?? []) ? route('jeje.destroy', $videos[99][0]->id) : 'javascript:void(0)';
                                    $disabled = empty($videos[99] ?? []);
                                @endphp
                                <a href="{{ $url99 }}" class="btn btn-sm btn-primary me-2 spa-link {{ $disabled ? 'disabled' : '' }}">
                                    Detail
                                    <i class="bi bi-arrow-right text-white"></i>
                                </a>
                            </div>
                            <div id="display-99" class="accordion-collapse collapse" data-bs-parent="#videos">
                                <div class="accordion-body">
                                    @forelse ($videos[99] ?? [] as $video)
                                        <div class="card">
                                            <video src="{{ asset('storage/jj/' . $video->filename) }}" class="card-img-top" controls></video>
                                            <div class="card-body">
                                                <p class="card-text">Durasi: {{ $video->duration }} detik | Size:
                                                    {{ formatSize($video->size) }}
                                                </p>
                                                <p class="card-text">
                                                    <small class="text-body-secondary">
                                                        {{ formatDate($video->updated_at) }}
                                                    </small>
                                                </p>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-muted">Belum ada video untuk jenis ini.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
