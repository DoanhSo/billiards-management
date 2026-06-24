{{-- resources/views/dashboard/admin.blade.php --}}
<div class="page-content-padding pt-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="page-title mb-0">Tổng Quan Dashboard</h1>
        <div>
            <span class="text-muted"><i class="bi bi-calendar-event me-1"></i> {{ now()->format('d/m/Y') }}</span>
        </div>
    </div>

    {{-- Row 1: Stat Cards --}}
    <div class="row">
        <div class="col-12 col-sm-6 col-xl-3">
            <x-stat-card 
                title="Doanh Thu Hôm Nay" 
                value="{{ number_format($stats['revenue']['today'], 0, ',', '.') }} ₫" 
                icon="bi-cash-coin" 
                color="success" />
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <x-stat-card 
                title="Doanh Thu Tháng Này" 
                value="{{ number_format($stats['revenue']['month'], 0, ',', '.') }} ₫" 
                icon="bi-graph-up-arrow" 
                color="primary" />
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <x-stat-card 
                title="Đặt Bàn Hôm Nay" 
                value="{{ $stats['bookings']['today'] }} Lượt" 
                icon="bi-calendar-check" 
                color="warning" />
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <x-stat-card 
                title="Bàn Đang Chơi" 
                value="{{ $stats['table_status']['PLAYING'] ?? 0 }} Bàn" 
                icon="bi-play-circle" 
                color="danger" />
        </div>
    </div>

    {{-- Row 2: Biểu đồ & Top Bàn Chơi --}}
    <div class="row">
        <div class="col-12 col-xl-8">
            <x-card>
                <x-slot:title>
                    <i class="bi bi-bar-chart me-2"></i>Doanh Thu 7 Ngày Gần Nhất
                </x-slot>
                <div style="height: 300px;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </x-card>
        </div>
        <div class="col-12 col-xl-4">
            <x-card>
                <x-slot:title>
                    <i class="bi bi-star me-2 text-warning"></i>Bàn Được Dùng Nhiều (Top 5)
                </x-slot>
                
                @if(empty($stats['popular_tables']))
                    <div class="text-center py-4">
                        <i class="bi bi-file-earmark-x fs-1 text-muted"></i>
                        <p class="text-muted mt-2">Chưa có dữ liệu</p>
                    </div>
                @else
                    <x-table>
                        <x-slot:thead>
                            <tr>
                                <th>Bàn</th>
                                <th class="text-end">Lượt Chơi</th>
                                <th class="text-end">Tổng Giờ</th>
                            </tr>
                        </x-slot>
                        @foreach($stats['popular_tables'] as $table)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $table['table_number'] }}</div>
                                    <small class="text-muted">{{ $table['table_type'] }}</small>
                                </td>
                                <td class="text-end">{{ $table['session_count'] }}</td>
                                <td class="text-end">{{ number_format($table['total_hours'], 1) }}h</td>
                            </tr>
                        @endforeach
                    </x-table>
                @endif
            </x-card>
        </div>
    </div>

    {{-- Row 3: Hóa Đơn & Top Sản Phẩm --}}
    <div class="row">
        <div class="col-12 col-xl-8">
            <x-card>
                <x-slot:title>
                    <i class="bi bi-receipt me-2"></i>10 Hóa Đơn Gần Nhất
                </x-slot>
                
                @if($stats['recent_invoices']->isEmpty())
                    <div class="text-center py-4">
                        <i class="bi bi-file-earmark-x fs-1 text-muted"></i>
                        <p class="text-muted mt-2">Chưa có dữ liệu</p>
                    </div>
                @else
                    <x-table>
                        <x-slot:thead>
                            <tr>
                                <th>Mã HĐ</th>
                                <th>Bàn</th>
                                <th>Thời Gian</th>
                                <th>Tổng Tiền</th>
                                <th>Trạng Thái</th>
                            </tr>
                        </x-slot>
                        @foreach($stats['recent_invoices'] as $invoice)
                            <tr>
                                <td><span class="fw-semibold">#{{ $invoice->id }}</span></td>
                                <td>{{ $invoice->tableSession->billiardTable->table_number ?? 'N/A' }}</td>
                                <td>{{ $invoice->created_at->format('d/m/Y H:i') }}</td>
                                <td class="fw-medium text-danger">{{ number_format($invoice->total_amount, 0, ',', '.') }} ₫</td>
                                <td>
                                    @if($invoice->payment_status === 'PAID')
                                        <x-badge type="success">Đã thanh toán</x-badge>
                                    @else
                                        <x-badge type="warning">Chưa thanh toán</x-badge>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </x-table>
                @endif
            </x-card>
        </div>
        <div class="col-12 col-xl-4">
            <x-card>
                <x-slot:title>
                    <i class="bi bi-bag-check me-2 text-success"></i>Sản Phẩm Bán Chạy (Top 5)
                </x-slot>
                
                @if(empty($stats['top_products']))
                    <div class="text-center py-4">
                        <i class="bi bi-file-earmark-x fs-1 text-muted"></i>
                        <p class="text-muted mt-2">Chưa có dữ liệu</p>
                    </div>
                @else
                    <x-table>
                        <x-slot:thead>
                            <tr>
                                <th>Sản Phẩm</th>
                                <th class="text-end">Đã Bán</th>
                                <th class="text-end">Doanh Thu</th>
                            </tr>
                        </x-slot>
                        @foreach($stats['top_products'] as $product)
                            <tr>
                                <td>
                                    <div class="fw-semibold text-truncate" style="max-width: 150px;">{{ $product['product_name'] }}</div>
                                    <small class="text-muted">{{ $product['category_name'] }}</small>
                                </td>
                                <td class="text-end">{{ $product['total_sold'] }}</td>
                                <td class="text-end">{{ number_format($product['total_revenue'], 0, ',', '.') }} ₫</td>
                            </tr>
                        @endforeach
                    </x-table>
                @endif
            </x-card>
        </div>
    </div>
</div>

@push('scripts')
{{-- Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script id="chart-data" type="application/json">
    {!! json_encode($stats['revenue']['chart']) !!}
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chartData = JSON.parse(document.getElementById('chart-data').textContent);
        
        if (chartData && chartData.length > 0) {
            const labels = chartData.map(item => item.date);
            const data = chartData.map(item => item.revenue);
            
            const ctx = document.getElementById('revenueChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Doanh thu (VNĐ)',
                        data: data,
                        backgroundColor: 'rgba(102, 126, 234, 0.8)',
                        borderColor: '#667eea',
                        borderWidth: 1,
                        borderRadius: 4,
                        barPercentage: 0.6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += new Intl.NumberFormat('vi-VN').format(context.parsed.y) + ' ₫';
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)',
                                borderDash: [5, 5]
                            },
                            ticks: {
                                color: '#94a3b8',
                                callback: function(value) {
                                    if (value >= 1000000) {
                                        return (value / 1000000) + ' Tr';
                                    } else if (value >= 1000) {
                                        return (value / 1000) + ' k';
                                    }
                                    return value;
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#94a3b8'
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush

