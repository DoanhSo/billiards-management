{{-- resources/views/invoices/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Tạo hóa đơn & Thanh toán')

@section('content')
<div class="container-fluid px-0" style="max-width: 1000px;">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title mb-1">Lập Hóa Đơn & Thanh Toán</h1>
            <p class="text-muted mb-0">Tính tiền giờ bàn chơi và ghi nhận sản phẩm dịch vụ đi kèm.</p>
        </div>
        <a href="{{ route('dashboard.index') }}" class="btn btn-outline-light d-flex align-items-center gap-2">
            <i class="bi bi-arrow-left"></i> Hủy & Quay lại
        </a>
    </div>

    <form action="{{ route('invoices.store') }}" method="POST" id="invoiceForm">
        @csrf
        <input type="hidden" name="table_session_id" value="{{ $session->id }}">

        <div class="row g-4">
            <!-- Cột trái: Thông tin tiền giờ & Dịch vụ đi kèm -->
            <div class="col-12 col-lg-7">
                {{-- Thông tin bàn & tiền giờ --}}
                <x-card>
                    <x-slot:title>
                        <i class="bi bi-info-circle me-2 text-primary"></i>Chi tiết tiền giờ: Bàn {{ $session->billiardTable->table_number }}
                    </x-slot:title>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <div class="text-muted small">Thời điểm vào:</div>
                            <div class="fw-semibold text-white">{{ $session->start_time->format('d/m/Y H:i') }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small">Thời điểm ra:</div>
                            <div class="fw-semibold text-white">{{ $session->end_time->format('d/m/Y H:i') }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small">Đơn giá bàn:</div>
                            <div class="fw-semibold text-white">{{ number_format($session->billiardTable->price_per_hour, 0, ',', '.') }} ₫/h</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small">Tổng thời gian:</div>
                            <div class="fw-semibold text-warning">{{ $session->total_hours }} giờ</div>
                        </div>
                    </div>

                    <div class="p-3 rounded bg-danger-opacity border border-danger-subtle d-flex align-items-center justify-content-between">
                        <span class="fw-bold text-white">Tổng tiền giờ chơi:</span>
                        <span class="h4 fw-bold text-danger mb-0" id="table-price-display" data-value="{{ $session->table_price }}">
                            {{ number_format($session->table_price, 0, ',', '.') }} ₫
                        </span>
                    </div>
                </x-card>

                {{-- Dịch vụ món ăn / đồ uống đi kèm --}}
                <x-card>
                    <x-slot:title>
                        <i class="bi bi-cart-plus me-2 text-success"></i>Thêm dịch vụ & sản phẩm
                    </x-slot:title>

                    <!-- Chọn sản phẩm -->
                    <div class="row g-2 mb-4">
                        <div class="col-6 col-sm-7">
                            <select id="product-select" class="form-select text-white border-secondary" style="background-color: rgba(255,255,255,0.07);">
                                <option value="" disabled selected>-- Chọn sản phẩm/dịch vụ --</option>
                                @foreach($products as $prod)
                                    <option value="{{ $prod->id }}" data-price="{{ $prod->price }}" data-name="{{ $prod->name }}" data-stock="{{ $prod->quantity }}">
                                        {{ $prod->name }} (Giá: {{ number_format($prod->price, 0, ',', '.') }} ₫ - Còn: {{ $prod->quantity }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-3 col-sm-2">
                            <input type="number" id="product-qty" class="form-control text-white border-secondary" value="1" min="1" style="background-color: rgba(255,255,255,0.07);">
                        </div>
                        <div class="col-3 col-sm-3">
                            <button type="button" id="btn-add-service" class="btn btn-success w-100">
                                <i class="bi bi-plus-lg"></i> Thêm
                            </button>
                        </div>
                    </div>

                    <!-- Bảng chi tiết dịch vụ đã chọn -->
                    <h5 class="text-white mb-2 fs-6">Danh sách dịch vụ đã gọi:</h5>
                    <div class="table-responsive">
                        <table class="table table-hover table-borderless text-white align-middle" id="services-table">
                            <thead style="font-size: 13px; text-transform: uppercase; color: var(--text-muted); border-bottom: 1px solid var(--border-color);">
                                <tr>
                                    <th>Sản Phẩm</th>
                                    <th class="text-end">Đơn Giá</th>
                                    <th class="text-center" style="width: 100px;">Số Lượng</th>
                                    <th class="text-end">Thành Tiền</th>
                                    <th class="text-center" style="width: 50px;"></th>
                                </tr>
                            </thead>
                            <tbody id="services-list">
                                <tr class="empty-row">
                                    <td colspan="5" class="text-center text-muted py-4">Chưa có dịch vụ đi kèm.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </x-card>
            </div>

            <!-- Cột phải: Tổng hợp hóa đơn & Thanh toán -->
            <div class="col-12 col-lg-5">
                <x-card>
                    <x-slot:title>
                        <i class="bi bi-receipt-cutoff me-2 text-warning"></i>Tổng hợp hóa đơn
                    </x-slot:title>

                    <div class="d-flex justify-content-between mb-2 small text-muted">
                        <span>Tiền giờ chơi:</span>
                        <span class="text-white">{{ number_format($session->table_price, 0, ',', '.') }} ₫</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 small text-muted">
                        <span>Tiền dịch vụ/đồ uống:</span>
                        <span class="text-white" id="services-total-display">0 ₫</span>
                    </div>

                    <hr class="border-secondary">

                    {{-- Chiết khấu / Giảm giá --}}
                    <div class="mb-3">
                        <label for="discount" class="form-label text-muted small">Chiết khấu / Giảm giá (VNĐ):</label>
                        <input type="number" name="discount" id="discount" class="form-control text-white border-secondary" placeholder="Nhập số tiền giảm giá..." min="0" value="0" style="background-color: rgba(255,255,255,0.07);">
                    </div>

                    {{-- Phương thức thanh toán --}}
                    <div class="mb-4">
                        <label for="payment_method" class="form-label text-muted small">Phương thức thanh toán <span class="text-danger">*</span></label>
                        <select name="payment_method" id="payment_method" class="form-select text-white border-secondary" required style="background-color: rgba(255,255,255,0.07);">
                            <option value="CASH" selected>TIỀN MẶT (CASH)</option>
                            <option value="BANKING">CHUYỂN KHOẢN (BANKING)</option>
                        </select>
                    </div>

                    <div class="p-3 rounded mb-4" style="background-color: rgba(255,255,255,0.05); border: 1px solid var(--border-color);">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-white fs-5">TỔNG THANH TOÁN:</span>
                            <span class="h3 fw-bold text-danger mb-0" id="total-amount-display">
                                {{ number_format($session->table_price, 0, ',', '.') }} ₫
                            </span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-danger w-100 py-3 fw-bold fs-5 shadow">
                        <i class="bi bi-check2-all me-2"></i> Xác Nhận & Thanh Toán
                    </button>
                </x-card>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const productSelect = document.getElementById('product-select');
        const productQty = document.getElementById('product-qty');
        const btnAddService = document.getElementById('btn-add-service');
        const servicesList = document.getElementById('services-list');
        const servicesTotalDisplay = document.getElementById('services-total-display');
        const tablePrice = parseFloat(document.getElementById('table-price-display').getAttribute('data-value'));
        const discountInput = document.getElementById('discount');
        const totalAmountDisplay = document.getElementById('total-amount-display');
        
        let addedServices = {}; // Quản lý {productId: {name, price, quantity}}
        
        // Cập nhật tổng thanh toán trên giao diện
        function calculateInvoiceTotals() {
            let servicesTotal = 0;
            for (let id in addedServices) {
                servicesTotal += addedServices[id].price * addedServices[id].quantity;
            }
            
            // Hiển thị tiền dịch vụ
            servicesTotalDisplay.textContent = new Intl.NumberFormat('vi-VN').format(servicesTotal) + ' ₫';
            
            // Tính tổng cộng sau giảm giá
            const discount = parseFloat(discountInput.value) || 0;
            const finalTotal = Math.max(0, (tablePrice + servicesTotal) - discount);
            
            totalAmountDisplay.textContent = new Intl.NumberFormat('vi-VN').format(finalTotal) + ' ₫';
        }

        // Render lại danh sách dịch vụ trong bảng
        function renderServicesTable() {
            // Xóa hết dòng trừ header hoặc check rỗng
            servicesList.innerHTML = '';
            
            let keys = Object.keys(addedServices);
            if (keys.length === 0) {
                servicesList.innerHTML = `
                    <tr class="empty-row">
                        <td colspan="5" class="text-center text-muted py-4">Chưa có dịch vụ đi kèm.</td>
                    </tr>
                `;
                calculateInvoiceTotals();
                return;
            }
            
            keys.forEach((productId, idx) => {
                const item = addedServices[productId];
                const rowTotal = item.price * item.quantity;
                
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>
                        <div class="fw-semibold text-white">${item.name}</div>
                        <!-- Hidden inputs for Form Submit -->
                        <input type="hidden" name="items[${idx}][product_id]" value="${productId}">
                        <input type="hidden" name="items[${idx}][unit_price]" value="${item.price}">
                        <input type="hidden" name="items[${idx}][quantity]" value="${item.quantity}">
                    </td>
                    <td class="text-end text-white-50">${new Intl.NumberFormat('vi-VN').format(item.price)} ₫</td>
                    <td class="text-center">
                        <input type="number" class="form-control form-control-sm text-center text-white border-secondary mx-auto qty-update" 
                                data-product-id="${productId}" value="${item.quantity}" min="1" max="${item.stock}" 
                                style="width: 70px; background-color: rgba(255,255,255,0.07);">
                    </td>
                    <td class="text-end fw-semibold text-white">${new Intl.NumberFormat('vi-VN').format(rowTotal)} ₫</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-link text-danger p-0 btn-remove-service" data-product-id="${productId}">
                            <i class="bi bi-trash-fill fs-5"></i>
                        </button>
                    </td>
                `;
                servicesList.appendChild(tr);
            });
            
            // Gắn sự kiện thay đổi số lượng động
            document.querySelectorAll('.qty-update').forEach(input => {
                input.addEventListener('change', function() {
                    const productId = this.getAttribute('data-product-id');
                    let qty = parseInt(this.value) || 1;
                    const maxStock = parseInt(this.getAttribute('max')) || 999;

                    if (qty < 1) qty = 1;
                    if (qty > maxStock) {
                        alert(`Chỉ còn ${maxStock} sản phẩm trong kho!`);
                        qty = maxStock;
                        this.value = maxStock;
                    }

                    addedServices[productId].quantity = qty;
                    renderServicesTable();
                });
            });

            // Gắn sự kiện xóa dòng dịch vụ
            document.querySelectorAll('.btn-remove-service').forEach(btn => {
                btn.addEventListener('click', function() {
                    const productId = this.getAttribute('data-product-id');
                    delete addedServices[productId];
                    renderServicesTable();
                });
            });

            calculateInvoiceTotals();
        }

        // Bấm nút thêm dịch vụ
        btnAddService.addEventListener('click', function() {
            const selectedOption = productSelect.options[productSelect.selectedIndex];
            if (!selectedOption || !productSelect.value) {
                alert('Vui lòng chọn một sản phẩm.');
                return;
            }

            const productId = productSelect.value;
            const productName = selectedOption.getAttribute('data-name');
            const productPrice = parseFloat(selectedOption.getAttribute('data-price'));
            const productStock = parseInt(selectedOption.getAttribute('data-stock'));
            const qty = parseInt(productQty.value) || 1;

            if (qty > productStock) {
                alert(`Không thể thêm! Tồn kho chỉ còn ${productStock} sản phẩm.`);
                return;
            }

            if (addedServices[productId]) {
                const newQty = addedServices[productId].quantity + qty;
                if (newQty > productStock) {
                    alert(`Không thể thêm! Tổng số lượng vượt quá tồn kho (${productStock} sản phẩm).`);
                    return;
                }
                addedServices[productId].quantity = newQty;
            } else {
                addedServices[productId] = {
                    name: productName,
                    price: productPrice,
                    quantity: qty,
                    stock: productStock
                };
            }

            // Reset input số lượng và select
            productQty.value = 1;
            productSelect.value = '';

            renderServicesTable();
        });

        // Bấm nhập discount
        discountInput.addEventListener('input', calculateInvoiceTotals);
    });
</script>
<style>
    .bg-danger-opacity {
        background-color: rgba(239, 68, 68, 0.1) !important;
    }
</style>
@endpush
