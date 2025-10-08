@extends('layouts.app')
@section('title', 'Ganti Video JJ')

@section('content')
    <div class="d-flex align-items-center mb-3">
        <a href="{{ route('home') }}" class="btn btn-sm d-flex align-items-center spa-link">
            <i class="bi bi-arrow-left-short fs-3"></i>
            <span class="fw-bold fs-5">Kembali</span>
        </a>
    </div>

    <div class="row">
        <div class="col-lg-9">
            <div class="card custom-card">
                <form action="{{ route('jeje.update', $video->id) }}" method="POST" enctype="multipart/form-data" data-ajax="true">
                    @csrf
                    @method('PUT')

                    <div class="card-header">
                        <div>
                            <div class="card-title">Ganti Video JJ</div>
                            <div class="card-text">{{ $video->display_type_label }}.</div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="file">Pilih File</label>
                            <input class="form-control" type="file" id="file" name="file" accept="video/*">
                            <p class="form-text">Video harus berukuran maksimal 3MB.</p>
                        </div>
                        <div>
                            <label for="display_type">Jenis Tampil</label>
                            <input type="hidden" name="display_type" value="{{ $video->display_type }}">
                            <select class="form-select" name="display_type" id="display_type" disabled>
                                <option value="{{ $video->display_type }}" selected>
                                    {{ $video->display_type_label }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary w-100">
                            Ganti Video
                        </button>
                    </div>
                </form>
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
        document.getElementById('file').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (!file) return;

            if (file.size > 5 * 1024 * 1024) {
                alert('File terlalu besar. Maksimal 5MB.');
                event.target.value = '';
                return;
            }

            const preview = document.getElementById('preview');
            const source = preview.querySelector('source');
            const url = URL.createObjectURL(file);
            source.src = url;
            preview.load();

            const cardText = preview.closest('.card').querySelector('.card-body p.card-text');

            const tempVideo = document.createElement('video');
            tempVideo.preload = 'metadata';
            tempVideo.src = url;

            tempVideo.onloadedmetadata = function() {
                const duration = Math.round(tempVideo.duration);
                const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
                cardText.textContent = `Durasi: ${duration} detik | Size: ${sizeMB} MB`;
            }
        });
    </script>
@endsection
