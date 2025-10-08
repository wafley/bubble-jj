@extends('layouts.app')
@section('title', 'Detail Pesanan')

@section('content')
    <div class="d-flex align-items-center mb-3">
        <a href="{{ route('home') }}" class="btn btn-sm d-flex align-items-center spa-link">
            <i class="bi bi-arrow-left-short fs-3"></i>
            <span class="fw-bold fs-5">Kembali</span>
        </a>
    </div>

    <div class="row">
        <div class="col">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="w-100 d-flex align-items-center justify-content-between">
                        <h6 class="card-title">Pesanan: <mark>{{ $order->user->name ?? $order->user->profile->username_1 }}</mark></h6>
                        @if ($order->status === 'pending')
                            <button type="button" class="btn btn-danger btn-cancel-order"
                                data-url="{{ route('orders.destroy', [$order->id, 'destroy' => 'order']) }}">
                                <i class='fe fe-x'></i>
                                Batalkan
                            </button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="fs-6 fst-italic">
                            Jenis pesanan: {{ $order->service->name }}
                        </h6>
                        <span class="badge text-bg-{{ $order->status_color }}">
                            {{ $order->status_label }}
                        </span>
                    </div>

                    <div class="mb-3">
                        <span class="fw-bold">
                            {{ $order->status === 'rejected' ? 'Alasan Penolakan:' : 'Catatan:' }}
                        </span>
                        <p>
                            {{ $order->status === 'rejected' ? $order->reject_reason : $order->notes }}
                        </p>
                    </div>

                    <div class="mb-3">
                        <span class="fw-bold">Files: </span>
                    </div>

                    <div class="row">
                        @forelse ($order->files as $file)
                            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                <div class="card custom-card">
                                    @if ($file->type === 'image')
                                        @php
                                            $src = asset("storage/orders/{$file->type}/{$file->filename}");
                                        @endphp
                                        <img src="{{ $src }}" class="card-img-top" alt="Order File">
                                    @elseif ($file->type === 'video')
                                        @php
                                            $src = asset("storage/orders/{$file->type}/{$file->filename}");
                                        @endphp
                                        <video class="card-img-top" controls>
                                            <source src="{{ $src }}" type="video/mp4">
                                        </video>
                                    @else
                                        <div class="d-flex align-items-center justify-content-center bg-light" style="height: 150px;">
                                            <i class="fe fe-file fs-1 text-secondary"></i>
                                        </div>
                                    @endif

                                    <div class="card-body d-flex flex-column">
                                        <h6 class="card-title text-truncate" title="{{ basename($file->filename) }}">{{ basename($file->filename) }}
                                        </h6>
                                        <p class="card-text mb-1">
                                            Durasi: {{ $file->duration ?? 0 }} detik <br>
                                            Ukuran: {{ formatSize($file->size) }}
                                        </p>
                                        @if ($order->status === 'pending')
                                            <button type="button" class="btn btn-danger mt-auto btn-delete-file"
                                                data-url="{{ route('orders.files.destroy', $file->id) }}">
                                                <i class='fe fe-trash'></i> Hapus
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted">File pesanan ini telah di hapus.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script data-partial="1">
        $(".btn-cancel-order").on("click", function(e) {
            e.preventDefault();
            const url = $(this).data('url');

            ajaxRequest({
                url: url,
                method: "DELETE",
                confirm: {
                    title: "Batalkan Pesanan?",
                    text: "Pesanan ini akan dibatalkan!",
                    confirmButtonText: "Ya, batalkan!",
                },
            });
        });

        $(".btn-delete-file").on("click", function(e) {
            e.preventDefault();
            const url = $(this).data('url');

            ajaxRequest({
                url: url,
                method: "DELETE",
                confirm: {
                    title: "Hapus File?",
                    text: "File ini akan dihapus permanen!",
                    confirmButtonText: "Ya, hapus!",
                },
            });
        });
    </script>
@endsection
