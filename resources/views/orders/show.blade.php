@extends('layouts.admin')
@section('title', 'Detail Pesanan')

@section('content')
    <div class="row">
        <div class="col">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="d-flex flex-column gap-3 w-100">
                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                            <h5 class="fw-bold fs-5">Pesanan <mark>{{ $order->user->profile->username_1 }}</mark></h5>
                            @if ($order->status === 'pending')
                                <div class="d-flex align-items-center gap-3">
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalReject">
                                        <i class='fe fe-x'></i>
                                        Tolak
                                    </button>
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalResult">
                                        <i class='fe fe-upload'></i>
                                        Upload hasil
                                    </button>
                                </div>
                            @endif
                        </div>
                        <div>
                            <div class="d-flex align-items-center justify-content-between">
                                <h6 class="fs-6">
                                    <strong>Jenis pesanan</strong>: <mark class="fst-italic">{{ $order->service->name }}</mark>
                                </h6>
                                <span class="badge text-bg-{{ $order->status_color }}">
                                    {{ $order->status_label }}
                                </span>
                            </div>
                            <h6 class="fs-6">
                                <strong>Jenis Tampil</strong>: <mark class="fst-italic">{{ $order->display_type_label }}</mark>
                            </h6>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="mb-3">
                        <span class="fw-bold">Catatan: </span>
                        <p>
                            {{ $order->notes }}
                        </p>
                    </div>
                    <div class="mb-3">
                        <span class="fw-bold">Files: </span>
                        <div class="table-responsive">
                            <table id="table-order" class="table mb-0 table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Filename</th>
                                        <th>Durasi</th>
                                        <th>Ukuran</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->files as $row => $file)
                                        <tr>
                                            <td>{{ $row }}</td>
                                            <td>
                                                <a href="{{ asset('storage/orders/' . $file->type . '/' . $file->filename) }}" class="text-primary"
                                                    target="_blank">
                                                    {{ $file->filename }}
                                                </a>
                                            </td>
                                            <td>{{ $file->duration ?? 0 }}</td>
                                            <td>{{ $file->size }}</td>
                                            <td>
                                                <a href="{{ asset('storage/orders/' . $file->type . '/' . $file->filename) }}"
                                                    class="btn btn-sm btn-success" download>
                                                    <i class='fe fe-download'></i>
                                                    Download
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('modal')
    <div class="modal fade" id="modalResult" tabindex="-1" aria-labelledby="modalResultLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="form-result" action="{{ route('orders.update', ['order' => $order->id, 'action' => 'result']) }}" method="POST"
                    enctype="multipart/form-data" data-ajax="true">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="modalResultLabel">Upload Hasil</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="file_result_input">Video JJ</label>
                            <input type="file" class="form-control" name="file_result" id="file_result_input" accept="video/*">
                        </div>
                        <div class="mb-3">
                            <label for="proof_payment">Bukti Transfer</label>
                            <input type="file" class="form-control" name="proof_payment" id="proof_payment" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalReject" tabindex="-1" aria-labelledby="modalRejectLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="form-reject" action="{{ route('orders.update', ['order' => $order->id, 'action' => 'reject']) }}" method="POST"
                    data-ajax="true">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="modalRejectLabel">Alasan Ditolak</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <label for="reject_reason">Alasan</label>
                        <textarea class="form-control" id="reject_reason" name="reject_reason" rows="3" placeholder="Masukkan alasan disini..."></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
