@extends('layouts.app')
@section('title', 'Upload JJ')

@section('content')
    <div class="d-flex align-items-center mb-3">
        <a href="{{ url()->previous() }}" class="btn btn-sm d-flex align-items-center spa-link">
            <i class="bi bi-arrow-left-short fs-3"></i>
            <span class="fw-bold fs-5">Kembali</span>
        </a>
    </div>

    <div class="row">
        <div class="col">
            <div class="card custom-card">
                <form id="form-upload" action="{{ route('orders.store', ['service' => $service->slug]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('POST')

                    <div class="card-header">
                        <div class="card-title">Upload {{ $service->name }}</div>
                    </div>
                    <div class="card-body pb-0">
                        <div class="mb-3">
                            <label for="file">Pilih File {{ $service->name }}</label>
                            <input class="form-control" type="file" id="file" name="{{ $service->type === 'image' ? 'files[]' : 'file' }}"
                                accept="{{ $service->type }}/*" {{ $service->type === 'image' ? 'multiple' : '' }}>
                            <div id="preview-container" class="d-flex flex-wrap mt-3 d-none"></div>
                        </div>
                        <div class="mb-3">
                            <label for="display_type">Pilih Jenis Tampil</label>
                            <select class="form-select" name="display_type" id="display_type">
                                <option selected disabled>=== Pilih Jenis Tampil JJ ===</option>
                                @foreach (\App\Models\Order::DISPLAY_TYPES as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($service->price !== 0)
                            <div class="mb-3">
                                <label for="notes">Request Tambahan</label>
                                <textarea class="form-control form-control-sm" id="notes" name="notes" rows="3"
                                    placeholder="Contoh: Minta diedit cerah, tambahkan musik, dll..."></textarea>
                            </div>
                        @endif
                        <div class="mb-3">
                            <span>Biaya Upload:</span>
                            <h4 class="fw-bold text-success">{{ $service->formatted_price }}</h4>
                        </div>
                        @if ($service->rules)
                            <div class="mb-3">
                                <h5 class="text-warning fw-bold">
                                    <i class="ti ti-alert-circle"></i>
                                    S&K (Syarat & Ketentuan):
                                </h5>
                                <ul>
                                    @foreach ($service->rules as $rule)
                                        <li>{{ $rule }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary w-100">
                            Upload sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/upload.js') }}" data-partial="1"></script>
    <script data-partial="1">
        initUploadHandler({
            formSelector: "#form-upload",
            previewSelector: "#preview-container",
            previewType: "{{ $service->type }}",
            multiple: {{ $service->type === 'image' ? 'true' : 'false' }},
        });
    </script>
@endsection
