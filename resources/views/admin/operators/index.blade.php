@extends('layouts.admin')
@section('title', 'Data Operator')

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
                            <a href="{{ route('operators.create') }}" class="btn btn-primary spa-link">
                                <i class="me-2 ti ti-user-plus"></i>
                                Tambah
                            </a>
                            <button type="button" id="refresh-btn" class="btn btn-success">
                                <i class="me-2 ti ti-rotate"></i>
                                Refresh
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="overflow-auto">
                                <table id="operators-table" class="table mb-0 table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>No. Telepon</th>
                                            <th>Status</th>
                                            <th>Tanggal dibuat</th>
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
        window.operatorsTable = window.operatorsTable || null;

        function initOperatorsTable() {
            if ($.fn.DataTable.isDataTable("#operators-table")) {
                $("#operators-table").DataTable().destroy();
            }

            window.operatorsTable = $("#operators-table").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('operators.data') }}",
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
                        data: "name",
                        name: "name"
                    },
                    {
                        data: "phone",
                        name: "phone"
                    },
                    {
                        data: "status",
                        name: "status"
                    },
                    {
                        data: "created_at",
                        name: "created_at"
                    },
                    {
                        data: "action",
                        name: "action",
                        orderable: false,
                        searchable: false,
                        width: "180px"
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

        initOperatorsTable();

        window.operatorsTable.on("preXhr.dt", function() {
            loader(true);
        }).on("xhr.dt", function() {
            loader(false);
        });

        // Refresh table
        $("#refresh-btn").on("click", function() {
            window.operatorsTable.ajax.reload();
        });

        // Filter status
        $("#filter-status").on("change", function() {
            window.operatorsTable.ajax.reload();
        });
    </script>
@endsection
