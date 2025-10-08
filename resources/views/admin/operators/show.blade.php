@extends('layouts.admin')
@section('title', 'Detail Operator')

@section('content')
    <div class="row">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header pb-0 mb-0">
                    <h4 class="card-title mb-0">Informasi Akun</h4>
                    <small class="text-muted">
                        Dibuat pada {{ formatDate($operator->created_at) }} -
                        Terakhir diperbarui {{ formatDate($operator->updated_at) }}
                    </small>
                </div>
                <div class="card-body pt-2">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h3 class="mb-0 mark">{{ $operator->name }}</h3>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <strong>No. Telepon:</strong>
                            <span class="float-end">{{ $operator->phone }}</span>
                        </li>
                        <li class="list-group-item">
                            <strong>Username:</strong>
                            <span class="float-end">{{ $operator->profile->username_1 }}</span>
                        </li>
                        <li class="list-group-item">
                            <strong>Role:</strong>
                            <span class="float-end">{{ $operator->role->label }}</span>
                        </li>
                        <li class="list-group-item">
                            <strong>Status Akun:</strong>
                            <span class="float-end">
                                <span class="badge text-bg-{{ $operator->status_color }}">
                                    {{ $operator->status_label }}
                                </span>
                            </span>
                        </li>
                        <li class="list-group-item">
                            <strong>No. Telepon Terverifikasi:</strong>
                            <span class="float-end">
                                {{ $operator->verified_at ? formatDate($operator->verified_at) : 'Belum' }}
                            </span>
                        </li>
                    </ul>
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route('operators.edit', $operator->id) }}" class="btn btn-sm btn-info spa-link">
                        <i class="bi bi-pencil-square"></i> Edit
                    </a>
                    <button class="btn btn-sm btn-danger btn-delete" data-url="{{ route('operators.destroy', $operator->id) }}">
                        <i class="bi bi-trash"></i> Hapus
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Aktivitas Terbaru</h5>
                </div>
                <div class="card-body">
                    @if ($activities->isEmpty())
                        <p class="text-muted">Belum ada data aktivitas untuk operator ini.</p>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach ($activities as $activity)
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <strong>{{ ucfirst($activity->description) }}</strong><br>
                                            <small class="text-muted">
                                                @if ($activity->subject)
                                                    Terhadap: {{ class_basename($activity->subject_type) }} (ID: {{ $activity->subject_id }})
                                                @endif
                                            </small>
                                        </div>
                                        <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                    </div>
                                    @if ($activity->properties && $activity->properties->isNotEmpty())
                                        <pre class="small text-muted mb-0">{{ json_encode($activity->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script data-partial="1">
        $(".btn-delete").on("click", function(e) {
            e.preventDefault();
            const url = $(this).data('url');

            ajaxRequest({
                url: url,
                method: "DELETE",
                confirm: {
                    title: "Hapus Operator Ini?",
                    text: "Data operator ini akan dihapus permanen bersama dengan aktivitas terkait!",
                    confirmButtonText: "Ya, hapus!",
                },
            });
        });
    </script>
@endsection
