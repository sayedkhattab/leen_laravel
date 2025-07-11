@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">الخدمات المميزة</h3>
                    <a href="{{ route('admin.promotional.featured-services.create') }}" class="btn btn-light">
                        <i class="bi bi-plus-lg"></i> إضافة خدمة مميزة
                    </a>
                </div>
                <div class="card-body">
                    @if(count($featuredServices) > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>اسم الخدمة</th>
                                    <th>نوع الخدمة</th>
                                    <th>مقدم الخدمة</th>
                                    <th>الترتيب</th>
                                    <th>الحالة</th>
                                    <th>تاريخ الانتهاء</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($featuredServices as $featuredService)
                                <tr>
                                    <td>{{ $featuredService->id }}</td>
                                    <td>
                                        @if($featuredService->service)
                                            {{ $featuredService->service->name }}
                                        @else
                                            <span class="text-danger">الخدمة غير موجودة</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($featuredService->service_type == 'home_service')
                                            <span class="badge bg-info">خدمة منزلية</span>
                                        @else
                                            <span class="badge bg-primary">خدمة استوديو</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($featuredService->service && $featuredService->service->seller)
                                            {{ $featuredService->service->seller->first_name }} {{ $featuredService->service->seller->last_name }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $featuredService->display_order }}</td>
                                    <td>
                                        @if($featuredService->is_active)
                                            <span class="badge bg-success">نشط</span>
                                        @else
                                            <span class="badge bg-danger">غير نشط</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($featuredService->expires_at)
                                            {{ $featuredService->expires_at->format('Y-m-d') }}
                                            @if($featuredService->expires_at < now())
                                                <span class="badge bg-danger">منتهي</span>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.promotional.featured-services.destroy', $featuredService->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من إزالة هذه الخدمة من المميزة؟')">
                                                <i class="bi bi-trash"></i> إزالة
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> لا توجد خدمات مميزة حتى الآن. <a href="{{ route('admin.promotional.featured-services.create') }}">إضافة خدمة مميزة</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 