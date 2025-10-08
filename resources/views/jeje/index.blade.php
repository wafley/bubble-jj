@extends('layouts.admin')
@section('title', 'Video JJ')

@section('content')
    <div class="row">
        <div class="col">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col d-flex align-items-center gap-3">
                            <div>
                                <label for="filter-status" class="form-label">Filter Status</label>
                                <select id="filter-status" class="form-select">
                                    <option value="">Semua Status</option>
                                    <option value="1">Aktif</option>
                                    <option value="0">Tidak Aktif</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col d-flex align-items-center gap-3">
                            <button type="button" id="refresh-btn" class="btn btn-success">
                                <i class="me-2 ti ti-rotate"></i>
                                Refresh
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="overflow-auto">
                                <table id="videos-table" class="table mb-0 table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Pemilik</th>
                                            <th>Jenis</th>
                                            <th>Filename</th>
                                            <th>Info</th>
                                            <th>Status</th>
                                            <th>Tanggal Upload</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('templates/libs/datatables/datatables.min.js') }}" data-partial="1"></script>
    <script data-partial="1">
        window.videosTable = window.videosTable || null;

        function initVideosTable() {
            if ($.fn.DataTable.isDataTable("#videos-table")) {
                $("#videos-table").DataTable().destroy();
            }

            window.videosTable = $("#videos-table").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('jeje.data') }}",
                    data: function(d) {
                        d.status = $("#filter-status").val();
                    },
                    error: function(xhr) {
                        if (xhr.status === 401 || xhr.status === 419) {
                            showToast("error", "Sesi login habis, silakan login ulang!");
                            window.location.href = "{{ route('login') }}";
                        }
                    }
                },
                columns: [{
                        data: "DT_RowIndex",
                        name: "DT_RowIndex",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "owner",
                        name: "owner"
                    },
                    {
                        data: "display_type",
                        name: "display_type"
                    },
                    {
                        data: "filename",
                        name: "filename"
                    },
                    {
                        data: "info",
                        name: "info",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "status",
                        name: "status",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "created_at",
                        name: "created_at",
                        width: "150px"
                    },
                    {
                        data: "action",
                        name: "action",
                        orderable: false,
                        searchable: false,
                    },
                ],
                order: [
                    [0, "desc"]
                ],
                pageLength: 5,
                lengthMenu: [
                    [5, 15, 30, 50, 75, 100],
                    [5, 15, 30, 50, 75, 100]
                ],
                autoWidth: false,
                language: {
                    url: "{{ asset('templates/js/i18n/id.json') }}"
                }
            });
        }

        initVideosTable();

        window.videosTable.on("preXhr.dt", function() {
            loader(true);
        }).on("xhr.dt", function() {
            loader(false);
        });

        // Refresh table
        $("#refresh-btn").on("click", function() {
            window.videosTable.ajax.reload();
        });

        // Filter status
        $("#filter-status").on("change", function() {
            window.videosTable.ajax.reload();
        });
    </script>

    <script data-partial="1">
        $(document).on("click", ".btn-toggle", function(e) {
            e.preventDefault();
            const btn = $(this);
            const url = $(this).data('url');
            const isActive = btn.text().trim() === 'Nonaktifkan';

            ajaxRequest({
                url: url,
                method: "PUT",
                confirm: {
                    title: isActive ? "Konfirmasi Nonaktifkan Video" : "Konfirmasi Aktifkan Video",
                    text: `Apakah Anda yakin ingin ${isActive ? 'nonaktifkan' : 'aktifkan'} video ini?`,
                    confirmButtonText: isActive ? "Ya, Nonaktifkan" : "Ya, Aktifkan",
                },
            });
        });
    </script>
@endsection
