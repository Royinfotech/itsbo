<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Treasurer Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/treasd.css') }}">
</head>
<body>

    <div class="dashboard-container">
        <div class="dashboard-header">Treasurer's Collection</div>

        <!-- Dashboard Content -->
        <div class="dashboard-content">
            <!-- Left: Collection Cards -->
            <div class="collection-container">
                <!-- Total Collection Card -->
                <div class="collection-card total-collection">
                    <div class="collection-title">Total Collection</div>
                    <div class="collection-amount">₱{{ number_format($totalPaid, 2) }}</div>
                </div>

                <!-- Individual Payment For Cards -->
                @foreach($payment_fors as $index => $payment_for)
                <div class="collection-card">
                    <div class="collection-title">{{ $payment_for->name }}</div>
                    <div class="collection-amount">₱{{ number_format($payment_for->total_paid, 2) }}</div>
                    <div class="collection-progress">
                        @php
                            $expectedTotal = $payment_for->amount * App\Models\Student::where('status', 'active')->count();
                            $progress = $expectedTotal > 0 ? ($payment_for->total_paid / $expectedTotal) * 100 : 0;
                        @endphp
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ min($progress, 100) }}%; background-color: {{ $chartColors[$index % count($chartColors)] }}"></div>
                        </div>
                        <span class="progress-text">{{ number_format($progress, 1) }}%</span>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Right: Chart Section -->
            <div class="chart-container">
                <canvas id="collectionChart"></canvas>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('collectionChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    data: @json($chartData),
                    backgroundColor: @json($chartColors),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    title: {
                        display: true,
                        text: 'Collection Distribution'
                    }
                }
            }
        });
    });
    </script>

</body>
</html>
