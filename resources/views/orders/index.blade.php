@extends('layouts.admin')
@section('title', 'Pesanan')

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
                                    <option value="pending">Pending</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
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
                            <div class="table-responsive">
                                <table id="orders-table" class="table mb-0 table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Pemesan</th>
                                            <th>Layanan</th>
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
        window.ordersTable = window.ordersTable || null;

        function initOrdersTable() {
            if ($.fn.DataTable.isDataTable("#orders-table")) {
                $("#orders-table").DataTable().destroy();
            }

            window.ordersTable = $("#orders-table").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('orders.data') }}",
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
                        data: "orderer",
                        name: "orderer"
                    },
                    {
                        data: "service",
                        name: "service"
                    },
                    {
                        data: "status",
                        name: "status",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "created_at",
                        name: "created_at"
                    },
                    {
                        data: "action",
                        name: "action",
                        orderable: false,
                        searchable: false
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
                language: {
                    url: "{{ asset('templates/js/i18n/id.json') }}"
                }
            });
        }

        initOrdersTable();

        window.ordersTable.on("preXhr.dt", function() {
            loader(true);
        }).on("xhr.dt", function() {
            loader(false);
        });

        // Refresh table
        $("#refresh-btn").on("click", function() {
            window.ordersTable.ajax.reload();
        });

        // Filter status
        $("#filter-status").on("change", function() {
            window.ordersTable.ajax.reload();
        });
    </script>
@endsection
