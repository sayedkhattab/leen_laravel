@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #2F3E3B; color: white;">
                    <h3 class="card-title mb-0">البانرات الإعلانية</h3>
                    <a href="{{ route('admin.promotional.banners.create') }}" class="btn btn-light">
                        <i class="bi bi-plus-lg"></i> إضافة بانر جديد
                    </a>
                </div>
                <div class="card-body">
                    @if(count($banners) > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الصورة</th>
                                    <th>العنوان</th>
                                    <th>العنوان الفرعي</th>
                                    <th>الجمهور المستهدف</th>
                                    <th>نوع الرابط</th>
                                    <th>الترتيب</th>
                                    <th>الحالة</th>
                                    <th>تاريخ الانتهاء</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($banners as $banner)
                                <tr>
                                    <td>{{ $banner->id }}</td>
                                    <td>
                                        <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="img-thumbnail banner-thumb">
                                    </td>
                                    <td>{{ $banner->title }}</td>
                                    <td>{{ $banner->subtitle ?? '-' }}</td>
                                    <td>
                                        @if($banner->target_audience == 'all')
                                            <span class="badge bg-info">الجميع</span>
                                        @elseif($banner->target_audience == 'customers')
                                            <span class="badge bg-success">العملاء</span>
                                        @elseif($banner->target_audience == 'sellers')
                                            <span class="badge bg-warning">مقدمي الخدمات</span>
                                        @endif
                                    </td>
                                    <td>
                                        @switch($banner->link_type)
                                            @case('url')
                                                <span class="badge bg-secondary">رابط خارجي</span>
                                                {{-- لا حاجة لعرض الرابط الكامل هنا --}}
                                                @break
                                            @case('seller')
                                                <span class="badge bg-primary">مقدم خدمة</span>
                                                {{-- اسم البائع مخفي حسب المطلوب --}}
                                                @break
                                            @case('home_service')
                                                <span class="badge bg-success">خدمة منزلية</span>
                                                {{-- اسم الخدمة مخفي --}}
                                                @break
                                            @case('studio_service')
                                                <span class="badge bg-info">خدمة استوديو</span>
                                                {{-- اسم الخدمة مخفي --}}
                                                @break
                                            @default
                                                <span class="badge bg-secondary">غير محدد</span>
                                        @endswitch
                                    </td>
                                    <td>{{ $banner->display_order }}</td>
                                    <td>
                                        @if($banner->is_active)
                                            <span class="badge bg-success">نشط</span>
                                        @else
                                            <span class="badge bg-danger">غير نشط</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($banner->expires_at)
                                            {{ $banner->expires_at->format('Y-m-d') }}
                                            @if($banner->expires_at < now())
                                                <span class="badge bg-danger">منتهي</span>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap" role="group">
                                            <a href="{{ route('admin.promotional.banners.edit', $banner->id) }}" class="btn btn-sm btn-warning action-btn" title="تعديل">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('admin.promotional.banners.destroy', $banner->id) }}" method="POST" class="d-inline action-btn">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger action-btn" title="حذف" onclick="return confirm('هل أنت متأكد من حذف هذا البانر؟')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> لا توجد بانرات إعلانية حتى الآن. <a href="{{ route('admin.promotional.banners.create') }}">إضافة بانر جديد</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.banner-thumb{
    width:120px;
    height:60px;
    object-fit:cover;
}
.action-btn{
    margin:0 3px 3px 0;
    border-radius:6px !important;
}
</style>
@endsection 