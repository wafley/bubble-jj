@extends('layouts.app')
@section('title', 'Detail Video ' . $video->display_type_label)

@section('content')
    <div class="d-flex align-items-center mb-3">
        <a href="{{ url()->previous() }}" class="btn btn-sm d-flex align-items-center spa-link">
            <i class="bi bi-arrow-left-short fs-3"></i>
            <span class="fw-bold fs-5">Kembali</span>
        </a>
    </div>

    <div class="row">
        <div class="col-lg-9">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="w-100 d-flex align-items-center justify-content-between">
                        <h6 class="card-title">{{ $video->display_type_label }}</mark></h6>
                        <div class="d-flex align-items-center gap-3">
                            <a href="{{ route('jeje.edit', $video->id) }}" class="btn btn-secondary spa-link">
                                <i class='fe fe-edit'></i> Ganti
                            </a>
                            <button type="button" class="btn btn-danger btn-delete-jj" data-url="{{ route('jeje.destroy', $video->id) }}">
                                <i class='fe fe-trash'></i> Hapus
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-3 mb-3">
                        <h6 class="fw-bold">Status:</h6>
                        <span class="badge text-bg-{{ $video->status_color }}" style="height: fit-content;">
                            {{ $video->status_label }}
                        </span>
                    </div>
                    <div class="d-flex gap-3 mb-3">
                        <h6 class="fw-bold">Filename:</h6>
                        <mark class="fst-italic">
                            {{ $video->filename }}
                        </mark>
                    </div>
                    <div class="d-flex gap-3 mb-3">
                        <h6 class="fw-bold">Ukuran:</h6>
                        <span class="fst-italic">
                            {{ formatSize($video->size) }}
                        </span>
                    </div>
                    <div class="d-flex gap-3 mb-3">
                        <h6 class="fw-bold">Durasi:</h6>
                        <span class="fst-italic">
                            {{ gmdate('i:s', $video->duration) }}
                        </span>
                    </div>
                    <div class="d-flex gap-3 mb-3">
                        <h6 class="fw-bold">Tanggal Upload:</h6>
                        <span class="fst-italic">
                            {{ formatDate($video->created_at) }}
                        </span>
                    </div>
                    <div class="d-flex gap-3 mb-3">
                        <h6 class="fw-bold">Tanggal Diganti:</h6>
                        <span class="fst-italic">
                            {{ formatDate($video->updated_at) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="card custom-card">
                <video id="preview" class="card-img-top" controls>
                    <source src="{{ asset("storage/jj/{$video->filename}") }}" type="video/mp4">
                </video>
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
        </div>
    </div>
@endsection

@section('scripts')
    <script data-partial="1">
        $(".btn-delete-jj").on("click", function(e) {
            e.preventDefault();
            const url = $(this).data('url');

            ajaxRequest({
                url: url,
                method: "DELETE",
                confirm: {
                    title: "Hapus Video JJ?",
                    text: "Video JJ ini akan dihapus permanen!",
                    confirmButtonText: "Ya, hapus!",
                },
            });
        });
    </script>
@endsection
