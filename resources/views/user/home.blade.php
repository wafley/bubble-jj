@extends('layouts.app')
@section('title', 'Home')

@section('content')
    <section id="profile-section" class="row">
        <div class="col">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Profile</div>
                </div>
                <div class="card-body">
                    <div class="alert alert-primary custom-alert-icon shadow-sm text-primary" role="alert">
                        <i class="fa fa-info-circle "></i>
                        Upload foto kamu dengan cara klik kotak foto dibawah ini, agar muncul saat kamu di room host mahakarya.
                    </div>

                    <div class="row mb-2">
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

                    <div class="row">
                        <div class="col">
                            <label class="form-label">Daftar Akun TikTok Kamu</label>
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <strong>Akun 1</strong>: <i class="fst-italic">{{ Auth::user()->profile->username_1 ?? '-' }}</i>
                                </li>
                                <li class="list-group-item">
                                    <strong>Akun 2</strong>: <i class="fst-italic">{{ Auth::user()->profile->username_2 ?? '-' }}</i>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('profile') }}" class="btn btn-primary w-100 spa-link">
                        Edit akun
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section id="service-section" class="row">
        <h3 class="fw-bold mb-3">Daftar Layanan</h3>
        @foreach ($services as $service)
            <div class="col-lg-4">
                <div class="card custom-card">
                    <div class="card-header d-flex align-items-center justify-content-between pb-1">
                        @if ($service->slug === 'free')
                            <h5 class="fw-bold">Upload Video Jedag Jedug</h5>
                        @else
                            <h4 class="fw-bold">{{ $service->name }}</h4>
                        @endif
                        <h6 class="fw-bold text-success">{{ $service->formatted_price }}</h6>
                    </div>
                    <div class="card-body pb-0">
                        <h6 class="text-muted fw-bold">
                            Keterangan:
                        </h6>
                        <p>
                            @if ($service->slug === 'free')
                                Upload video JJ dengan format MP4 yang sudah jadi dan maksimal 3MB.
                            @else
                                {{ $service->description }}
                            @endif
                        </p>
                    </div>
                    <div class="card-footer">
                        @if ($service->slug === 'free')
                            <a href="{{ route('jeje.create') }}" class="btn btn-primary spa-link w-100">
                                Upload Sekarang
                            </a>
                        @else
                            <a href="{{ route('orders.create', ['service' => $service->slug]) }}" class="btn btn-primary spa-link w-100">
                                Pilih
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </section>

    <section id="other-menu" class="row">
        <h3 class="fw-bold mb-3">Lainnya</h3>
        <div class="card custom-card">
            <div class="card-header">
                <ul class="nav nav-pills justify-content-start nav-style-2" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" role="tab" aria-current="page" href="#videos" aria-selected="true">
                            Video JJ Kamu
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" role="tab" aria-current="page" href="#history">
                            Riwayat Pesanan
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane show active" id="videos" role="tabpanel">
                        <div class="accordion" id="jj">
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
                                <div id="display-10" class="accordion-collapse collapse" data-bs-parent="#jj">
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
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#display-20" aria-expanded="false" aria-controls="display-20">
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
                                <div id="display-20" class="accordion-collapse collapse" data-bs-parent="#jj">
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
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#display-30" aria-expanded="false" aria-controls="display-30">
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
                                <div id="display-30" class="accordion-collapse collapse" data-bs-parent="#jj">
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
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#display-99" aria-expanded="false" aria-controls="display-99">
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
                                <div id="display-99" class="accordion-collapse collapse" data-bs-parent="#jj">
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
                    <div class="tab-pane" id="history" role="tabpanel">
                        @if ($orders->isEmpty())
                            <p class="text-center text-muted mt-3">Belum ada riwayat order.</p>
                        @else
                            <div class="row gap-3">
                                @foreach ($orders as $order)
                                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                        <div class="card h-100">
                                            <div class="card-body d-flex flex-column">
                                                <span class="font-monospace text-secondary mb-1">
                                                    {{ formatDate($order->created_at) }}
                                                </span>
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <h5 class="fw-bold mb-2">{{ $order->service->name }}</h5>
                                                    <span class="badge text-bg-{{ $order->status_color }} mb-2">
                                                        {{ $order->status_label }}
                                                    </span>
                                                </div>
                                                <h6 class="fw-bold text-success mb-2">{{ $order->service->formatted_price }}</h6>

                                                <div class="mt-auto d-flex gap-2 flex-wrap">
                                                    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-primary w-100 spa-link">
                                                        Detail
                                                        <i class="fe fe-corner-down-right text-white"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
