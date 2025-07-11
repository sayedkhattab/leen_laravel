@extends('admin.layouts.app')

@section('title', 'الحجوزات - لوحة تحكم لين')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">الحجوزات</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">جميع الحجوزات</h6>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <i class="icon fas fa-check"></i> {{ session('success') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>رقم الحجز</th>
                            <th>نوع الخدمة</th>
                            <th>اسم الخدمة</th>
                            <th>العميل</th>
                            <th>مقدم الخدمة</th>
                            <th class="text-center">السعر</th>
                            <th>تاريخ الحجز</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bookings as $booking)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $booking->booking_number }}</td>
                                <td>
                                    @if ($booking->service_type == 'home')
                                        <span class="badge bg-primary">خدمة منزلية</span>
                                    @else
                                        <span class="badge bg-info">خدمة استوديو</span>
                                    @endif
                                </td>
                                <td>{{ $booking->service->name }}</td>
                                <td>{{ $booking->customer->first_name }} {{ $booking->customer->last_name }}</td>
                                <td>{{ $booking->service->seller->first_name }} {{ $booking->service->seller->last_name }}</td>
                                <td class="text-center">{{ $booking->total_price }} ريال</td>
                                <td>{{ $booking->booking_date->format('Y-m-d') }} {{ $booking->booking_time }}</td>
                                <td>
                                    @if ($booking->status == 'pending')
                                        <span class="badge bg-warning">قيد الانتظار</span>
                                    @elseif ($booking->status == 'confirmed')
                                        <span class="badge bg-success">مؤكد</span>
                                    @elseif ($booking->status == 'completed')
                                        <span class="badge bg-primary">مكتمل</span>
                                    @elseif ($booking->status == 'cancelled')
                                        <span class="badge bg-danger">ملغي</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="#" class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">لا توجد حجوزات</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 