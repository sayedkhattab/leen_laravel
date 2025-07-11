@extends('admin.layouts.app')

@section('title', 'الخدمات - لوحة تحكم لين')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">الخدمات</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">جميع الخدمات</h6>
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
                            <th>الصورة</th>
                            <th>اسم الخدمة</th>
                            <th>نوع الخدمة</th>
                            <th>مقدم الخدمة</th>
                            <th>القسم</th>
                            <th>القسم الفرعي</th>
                            <th class="text-center">السعر</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($services as $service)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    @php
                                        // جلب أول صورة متاحة مع دعم تنسيقات متعددة للحقل
                                        $mainImage = null;
                                        // إذا كان images مصفوفة
                                        if (is_array($service->images) && count($service->images) > 0) {
                                            $mainImage = $service->images[0];
                                        }
                                        // إذا كان images نص JSON
                                        elseif (is_string($service->images) && !empty($service->images)) {
                                            $decoded = json_decode($service->images, true);
                                            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded) && count($decoded) > 0) {
                                                $mainImage = $decoded[0];
                                            } else {
                                                // قد يكون اسم ملف واحد محفوظ كنص عادي
                                                $mainImage = $service->images;
                                            }
                                        }
                                        // دعم الحقل القديم "image" إذا كان موجودًا
                                        if (!$mainImage && !empty($service->image)) {
                                            $mainImage = $service->image;
                                        }
                                        $folder = $service->type === 'home' ? 'images/home_services/' : 'images/studio_services/';
                                        // تحضير المسار النهائي للصورة
                                        $imgSrc = null;
                                        if ($mainImage) {
                                            if (\Illuminate\Support\Str::startsWith($mainImage, ['http://', 'https://', 'images/'])) {
                                                $imgSrc = $mainImage; // لا تضف المجلد
                                            } else {
                                                $imgSrc = $folder . $mainImage;
                                            }
                                        }
                                    @endphp
                                    @if ($mainImage)
                                        <img src="{{ asset($imgSrc) }}" alt="{{ $service->name }}" style="width:50px;height:50px;object-fit:cover;" class="img-thumbnail">
                                    @else
                                        <img src="{{ asset('images/logo/green_logo.png') }}" alt="Default" style="width:50px;height:50px;object-fit:cover;" class="img-thumbnail">
                                    @endif
                                </td>
                                <td>{{ $service->name }}</td>
                                <td>
                                    @if ($service->type == 'home')
                                        <span class="badge bg-primary">خدمة منزلية</span>
                                    @else
                                        <span class="badge bg-info">خدمة استوديو</span>
                                    @endif
                                </td>
                                <td>{{ $service->seller->first_name }} {{ $service->seller->last_name }}</td>
                                <td>{{ $service->subCategory->category->name }}</td>
                                <td>{{ $service->subCategory->name }}</td>
                                <td class="text-center">{{ $service->price }} ريال</td>
                                <td>
                                    @if ($service->status == 'active')
                                        <span class="badge bg-success">نشط</span>
                                    @else
                                        <span class="badge bg-danger">غير نشط</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        @if ($service->type == 'home')
                                            <a href="{{ route('admin.services.show', ['type' => $service->type, 'id' => $service->id]) }}" class="btn btn-sm btn-info">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('admin.services.show', ['type' => $service->type, 'id' => $service->id]) }}" class="btn btn-sm btn-info">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">لا توجد خدمات</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 