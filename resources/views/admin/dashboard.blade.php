@extends('admin.layouts.app')

@section('title', 'لوحة التحكم - لين')

@section('content')
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card stat-card stat-customers">
                <div class="stat-title">عدد العملاء</div>
                <div class="stat-number">{{ $customersCount }}</div>
                <div class="stat-icon"><i class="bi bi-people"></i></div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card stat-card stat-sellers">
                <div class="stat-title">عدد مقدمي الخدمات</div>
                <div class="stat-number">{{ $sellersCount }}</div>
                <div class="stat-icon"><i class="bi bi-shop"></i></div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card stat-card stat-services">
                <div class="stat-title">إجمالي الخدمات</div>
                <div class="stat-number">{{ $servicesCount }}</div>
                <div class="stat-icon"><i class="bi bi-briefcase"></i></div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card stat-card stat-bookings">
                <div class="stat-title">إجمالي الحجوزات</div>
                <div class="stat-number">{{ $bookingsCount }}</div>
                <div class="stat-icon"><i class="bi bi-calendar-check"></i></div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">إحصائيات الحجوزات</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="bookingsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">توزيع الخدمات</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="servicesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">أحدث مقدمي الخدمات</h6>
                    <a href="{{ route('admin.sellers.index') }}" class="btn btn-sm btn-primary">عرض الكل</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>الاسم</th>
                                    <th>البريد الإلكتروني</th>
                                    <th>الحالة</th>
                                    <th>تاريخ التسجيل</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestSellers as $seller)
                                    <tr>
                                        <td>{{ $seller->first_name }} {{ $seller->last_name }}</td>
                                        <td>{{ $seller->email }}</td>
                                        <td>
                                            @if($seller->status == 'active')
                                                <span class="badge bg-success">نشط</span>
                                            @else
                                                <span class="badge bg-danger">غير نشط</span>
                                            @endif
                                        </td>
                                        <td>{{ $seller->created_at->format('Y-m-d') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">لا يوجد بيانات</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">أحدث العملاء</h6>
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-sm btn-primary">عرض الكل</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>الاسم</th>
                                    <th>البريد الإلكتروني</th>
                                    <th>الحالة</th>
                                    <th>تاريخ التسجيل</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestCustomers as $customer)
                                    <tr>
                                        <td>{{ $customer->first_name }} {{ $customer->last_name }}</td>
                                        <td>{{ $customer->email }}</td>
                                        <td>
                                            @if($customer->status == 'active')
                                                <span class="badge bg-success">نشط</span>
                                            @else
                                                <span class="badge bg-danger">غير نشط</span>
                                            @endif
                                        </td>
                                        <td>{{ $customer->created_at->format('Y-m-d') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">لا يوجد بيانات</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">أحدث الحجوزات</h6>
                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-primary">عرض الكل</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>رقم الحجز</th>
                                    <th>العميل</th>
                                    <th>مقدم الخدمة</th>
                                    <th>الخدمة</th>
                                    <th>التاريخ</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestBookings as $booking)
                                    <tr>
                                        <td>{{ $booking->id }}</td>
                                        <td>{{ $booking->customer->first_name }} {{ $booking->customer->last_name }}</td>
                                        <td>{{ $booking->service->seller->first_name }} {{ $booking->service->seller->last_name }}</td>
                                        <td>{{ $booking->service->title }}</td>
                                        <td>{{ $booking->booking_date->format('Y-m-d') }}</td>
                                        <td>
                                            @if($booking->status == 'pending')
                                                <span class="badge bg-warning">قيد الانتظار</span>
                                            @elseif($booking->status == 'confirmed')
                                                <span class="badge bg-success">مؤكد</span>
                                            @elseif($booking->status == 'completed')
                                                <span class="badge bg-primary">مكتمل</span>
                                            @elseif($booking->status == 'cancelled')
                                                <span class="badge bg-danger">ملغي</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">لا يوجد بيانات</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // بيانات الرسم البياني للحجوزات (بيانات افتراضية)
    const bookingsCtx = document.getElementById('bookingsChart').getContext('2d');
    const bookingsChart = new Chart(bookingsCtx, {
        type: 'line',
        data: {
            labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'],
            datasets: [
                {
                    label: 'الحجوزات المنزلية',
                    data: [12, 19, 3, 5, 2, 3],
                    backgroundColor: 'rgba(47, 62, 59, 0.2)',
                    borderColor: 'rgba(47, 62, 59, 1)',
                    borderWidth: 2,
                    tension: 0.4
                },
                {
                    label: 'حجوزات الاستوديو',
                    data: [5, 10, 8, 15, 12, 9],
                    backgroundColor: 'rgba(255, 173, 201, 0.2)',
                    borderColor: 'rgba(255, 173, 201, 1)',
                    borderWidth: 2,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // بيانات الرسم البياني لتوزيع الخدمات (بيانات افتراضية)
    const servicesCtx = document.getElementById('servicesChart').getContext('2d');
    const servicesChart = new Chart(servicesCtx, {
        type: 'doughnut',
        data: {
            labels: ['خدمات منزلية', 'خدمات استوديو'],
            datasets: [{
                data: [{{ $homeServicesCount }}, {{ $studioServicesCount }}],
                backgroundColor: [
                    'rgba(47, 62, 59, 0.8)',
                    'rgba(255, 173, 201, 0.8)'
                ],
                borderColor: [
                    'rgba(47, 62, 59, 1)',
                    'rgba(255, 173, 201, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
@endsection 