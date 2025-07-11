@extends('admin.layouts.app')

@section('title', 'تفاصيل العميل - لوحة تحكم لين')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-gradient-info">
                    <h3 class="card-title text-white">تفاصيل العميل</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.customers.index') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left"></i> العودة
                        </a>
                        <a href="{{ route('admin.customers.edit', $customer->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> تعديل
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center mb-4">
                                @if ($customer->image)
                                    <img src="{{ asset($customer->image) }}" alt="{{ $customer->first_name }}" class="img-fluid rounded img-thumbnail" style="max-height: 300px;">
                                @else
                                    <img src="{{ asset('images/logo/green_logo.png') }}" alt="Default" class="img-fluid rounded img-thumbnail" style="max-height: 300px;">
                                @endif
                            </div>

                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">معلومات سريعة</h3>
                                </div>
                                <div class="card-body box-profile">
                                    <h3 class="profile-username text-center">{{ $customer->first_name }} {{ $customer->last_name }}</h3>
                                    <p class="text-muted text-center">{{ $customer->email }}</p>
                                    
                                    <ul class="list-group list-group-unbordered mb-3">
                                        <li class="list-group-item">
                                            <b>رقم الهاتف</b> <a class="float-left">{{ $customer->phone }}</a>
                                        </li>
                                        <li class="list-group-item">
                                            <b>الحالة</b> 
                                            <span class="float-left">
                                                <span class="badge badge-{{ $customer->status == 'active' ? 'success' : 'danger' }} p-2">
                                                    {{ $customer->status == 'active' ? 'نشط' : 'غير نشط' }}
                                                </span>
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
                                            <th style="width: 30%">الاسم الأول</th>
                                            <td>{{ $customer->first_name }}</td>
                                        </tr>
                                        <tr>
                                            <th>الاسم الأخير</th>
                                            <td>{{ $customer->last_name }}</td>
                                        </tr>
                                        <tr>
                                            <th>البريد الإلكتروني</th>
                                            <td>{{ $customer->email }}</td>
                                        </tr>
                                        <tr>
                                            <th>رقم الهاتف</th>
                                            <td>{{ $customer->phone }}</td>
                                        </tr>
                                        <tr>
                                            <th>حالة التحقق من الهاتف</th>
                                            <td>
                                                @if($customer->phone_verified_at)
                                                    <span class="badge badge-success p-2">تم التحقق</span>
                                                @else
                                                    <span class="badge badge-warning p-2">لم يتم التحقق</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>الموقع</th>
                                            <td>{{ $customer->location ?? 'غير محدد' }}</td>
                                        </tr>
                                        <tr>
                                            <th>الحالة</th>
                                            <td>
                                                <span class="badge badge-{{ $customer->status == 'active' ? 'success' : 'danger' }} p-2">
                                                    {{ $customer->status == 'active' ? 'نشط' : 'غير نشط' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>تاريخ التسجيل</th>
                                            <td>{{ $customer->created_at->format('Y-m-d H:i') }}</td>
                                        </tr>
                                    </table>

                                    <div class="mt-4">
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                            <i class="fas fa-trash mr-1"></i> حذف العميل
                                        </button>
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

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">تأكيد الحذف</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                هل أنت متأكد من حذف العميل "{{ $customer->first_name }} {{ $customer->last_name }}"؟
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <form action="{{ route('admin.customers.destroy', $customer->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">حذف</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 