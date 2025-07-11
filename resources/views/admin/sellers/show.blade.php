@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-gradient-info">
                    <h3 class="card-title text-white">تفاصيل مقدم الخدمة</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.sellers.index') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left"></i> العودة
                        </a>
                        <a href="{{ route('admin.sellers.edit', $seller->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> تعديل
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center mb-4">
                                @if ($seller->seller_logo)
                                    <img src="{{ asset($seller->seller_logo) }}" alt="{{ $seller->first_name }}" class="img-fluid rounded img-thumbnail" style="max-height: 300px;">
                                @else
                                    <img src="{{ asset('images/logo/green_logo.png') }}" alt="Default" class="img-fluid rounded img-thumbnail" style="max-height: 300px;">
                                @endif
                            </div>

                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">معلومات سريعة</h3>
                                </div>
                                <div class="card-body box-profile">
                                    <h3 class="profile-username text-center">{{ $seller->first_name }} {{ $seller->last_name }}</h3>
                                    <p class="text-muted text-center">{{ $seller->email }}</p>
                                    
                                    <ul class="list-group list-group-unbordered mb-3">
                                        <li class="list-group-item">
                                            <b>رقم الهاتف</b> <a class="float-left">{{ $seller->phone }}</a>
                                        </li>
                                        <li class="list-group-item">
                                            <b>الحالة</b> 
                                            <span class="float-left">
                                                @if ($seller->request_status === 'pending')
                                                    <span class="badge badge-warning p-2">قيد الانتظار</span>
                                                @elseif ($seller->request_status === 'approved')
                                                    <span class="badge badge-success p-2">تمت الموافقة</span>
                                                @elseif ($seller->request_status === 'rejected')
                                                    <span class="badge badge-danger p-2">مرفوض</span>
                                                @endif
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">التفاصيل الكاملة</h3>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered table-striped">
                                        <tr>
                                            <th style="width: 30%">الاسم</th>
                                            <td>{{ $seller->first_name }} {{ $seller->last_name }}</td>
                                        </tr>
                                        <tr>
                                            <th>البريد الإلكتروني</th>
                                            <td>{{ $seller->email }}</td>
                                        </tr>
                                        <tr>
                                            <th>رقم الهاتف</th>
                                            <td>{{ $seller->phone }}</td>
                                        </tr>
                                        <tr>
                                            <th>العنوان</th>
                                            <td>{{ $seller->location ?? 'غير محدد' }}</td>
                                        </tr>
                                        <tr>
                                            <th>الحالة</th>
                                            <td>
                                                @if ($seller->request_status === 'pending')
                                                    <span class="badge badge-warning p-2">قيد الانتظار</span>
                                                @elseif ($seller->request_status === 'approved')
                                                    <span class="badge badge-success p-2">تمت الموافقة</span>
                                                @elseif ($seller->request_status === 'rejected')
                                                    <span class="badge badge-danger p-2">مرفوض</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>تاريخ التسجيل</th>
                                            <td>{{ $seller->created_at->format('Y-m-d H:i') }}</td>
                                        </tr>
                                    </table>

                                    @if ($seller->request_status === 'pending')
                                        <div class="mt-4">
                                            <form action="{{ route('admin.sellers.approve', $seller->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-lg">
                                                    <i class="fas fa-check mr-1"></i> الموافقة على مقدم الخدمة
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.sellers.reject', $seller->id) }}" method="POST" class="d-inline mr-2">
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-lg">
                                                    <i class="fas fa-times mr-1"></i> رفض مقدم الخدمة
                                                </button>
                                            </form>
                                        </div>
                                    @endif

                                    <div class="mt-4">
                                        <form action="{{ route('admin.sellers.destroy', $seller->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('هل أنت متأكد من حذف هذا المقدم؟')">
                                                <i class="fas fa-trash mr-1"></i> حذف مقدم الخدمة
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 