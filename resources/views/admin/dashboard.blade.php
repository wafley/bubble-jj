@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
    {{-- Filter tanggal --}}
    <form method="GET" action="{{ route('dashboard') }}" class="mb-4 d-flex gap-3">
        <div>
            <label for="start_date">Start Date</label>
            <input type="date" name="start_date" value="{{ $startDate }}" class="form-control">
        </div>
        <div>
            <label for="end_date">End Date</label>
            <input type="date" name="end_date" value="{{ $endDate }}" class="form-control">
        </div>
        <div class="d-flex align-items-end">
            <button type="submit" class="btn btn-primary">Filter</button>
        </div>
    </form>

    {{-- Charts --}}
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">Aktivitas Harian</div>
                <div class="card-body">
                    <canvas id="logsByDateChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">Aktivitas Berdasarkan Jenis</div>
                <div class="card-body">
                    <canvas id="logsByTypeChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Log Detail --}}
    <div class="card">
        <div class="card-header">Detail Aktivitas</div>
        <div class="card-body table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>User</th>
                        <th>Event</th>
                        <th>Log Type</th>
                        <th>Properties</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logs as $log)
                        <tr>
                            <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                            <td>{{ $log->causer?->name ?? '-' }}</td>
                            <td>{{ $log->description }}</td>
                            <td><span class="badge bg-info">{{ $log->log_name }}</span></td>
                            <td>
                                <pre>{{ json_encode($log->properties, JSON_PRETTY_PRINT) }}</pre>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('templates/libs/chart.js/chart.min.js') }}" data-partial="1"></script>
    <script src="{{ asset('templates/libs/apexcharts/apexcharts.min.js') }}" data-partial="1"></script>

    <script data-partial="1">
        // Data dari Laravel
        const logsByDate = @json($logsByDate);
        const logsByType = @json($logsByType);

        // Chart: Aktivitas Harian
        const ctx1 = document.getElementById('logsByDateChart').getContext('2d');
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: logsByDate.map(item => item.date),
                datasets: [{
                    label: 'Jumlah Aktivitas',
                    data: logsByDate.map(item => item.total),
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            }
        });

        // Chart: Aktivitas Berdasarkan Jenis
        const ctx2 = document.getElementById('logsByTypeChart').getContext('2d');
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: logsByType.map(item => item.log_name),
                datasets: [{
                    label: 'Jumlah',
                    data: logsByType.map(item => item.total)
                }]
            }
        });
    </script>
@endsection
