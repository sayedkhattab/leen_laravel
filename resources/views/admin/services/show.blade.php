@extends('admin.layouts.app')

@section('title', 'تفاصيل الخدمة - لوحة تحكم لين')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">تفاصيل الخدمة: {{ $service->name }}</h1>
        <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-right"></i> العودة إلى جميع الخدمات
        </a>
    </div>

    <div class="row">
        <div class="col-md-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">معلومات الخدمة</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        @php
                            $mainImage = null;
                            if (is_array($service->images) && count($service->images) > 0) {
                                $mainImage = $service->images[0];
                            } elseif (is_string($service->images) && !empty($service->images)) {
                                $decoded = json_decode($service->images, true);
                                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded) && count($decoded) > 0) {
                                    $mainImage = $decoded[0];
                                } else {
                                    $mainImage = $service->images;
                                }
                            }
                            if (!$mainImage && !empty($service->image)) {
                                $mainImage = $service->image;
                            }
                            $folder = $service->type === 'home' ? 'images/home_services/' : 'images/studio_services/';
                            $imgSrc = null;
                            if ($mainImage) {
                                if (\Illuminate\Support\Str::startsWith($mainImage, ['http://', 'https://', 'images/'])) {
                                    $imgSrc = $mainImage;
                                } else {
                                    $imgSrc = $folder . $mainImage;
                                }
                            }
                        @endphp
                        @if($mainImage)
                            <img src="{{ asset($imgSrc) }}" alt="{{ $service->name }}" class="img-fluid rounded" style="width:200px;height:200px;object-fit:cover;">
                        @else
                            <div class="bg-light p-5 rounded">
                                <i class="bi bi-image text-secondary" style="font-size: 5rem;"></i>
                                <p class="mt-2 text-muted">لا توجد صورة</p>
                            </div>
                        @endif
                    </div>

                    <table class="table">
                        <tr>
                            <th>الاسم</th>
                            <td>{{ $service->name }}</td>
                        </tr>
                        <tr>
                            <th>النوع</th>
                            <td>
                                @if($service->type === 'home')
                                    <span class="badge bg-primary">خدمة منزلية</span>
                                @else
                                    <span class="badge bg-info">خدمة استوديو</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>السعر</th>
                            <td>{{ $service->price }} ريال</td>
                        </tr>
                        <tr>
                            <th>المدة</th>
                            <td>{{ $service->duration ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>التصنيف</th>
                            <td>
                                <a href="{{ route('admin.categories.show', $service->subCategory->category_id) }}">
                                    {{ $service->subCategory->category->name }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>التصنيف الفرعي</th>
                            <td>
                                <a href="{{ route('admin.subcategories.show', $service->sub_category_id) }}">
                                    {{ $service->subCategory->name }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>مقدم الخدمة</th>
                            <td>
                                <a href="{{ route('admin.sellers.show', $service->seller_id) }}">
                                    {{ $service->seller->first_name }} {{ $service->seller->last_name }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>الحالة</th>
                            <td>
                                @if($service->status === 'active')
                                    <span class="badge bg-success">نشط</span>
                                @else
                                    <span class="badge bg-danger">غير نشط</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>تاريخ الإنشاء</th>
                            <td>{{ $service->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                        <tr>
                            <th>آخر تحديث</th>
                            <td>{{ $service->updated_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">الوصف والتفاصيل</h6>
                </div>
                <div class="card-body">
                    <h6 class="font-weight-bold">الوصف:</h6>
                    <p>{{ $service->description ?: 'لا يوجد وصف' }}</p>

                    <hr>

                    <h6 class="font-weight-bold">تفاصيل الخدمة:</h6>
                    <p>{{ $service->service_details ?: 'لا توجد تفاصيل' }}</p>

                    @if($service->images && is_array($service->images) && count($service->images) > 0)
                        <hr>
                        <h6 class="font-weight-bold">معرض الصور:</h6>
                        <div class="row">
                            @foreach($service->images as $img)
                                <div class="col-6 col-md-4 mb-3">
                                    @php
                                        if (\Illuminate\Support\Str::startsWith($img, ['http://', 'https://', 'images/'])) {
                                            $imgPath = $img;
                                        } else {
                                            $imgPath = $folder . $img;
                                        }
                                    @endphp
                                    <img src="{{ asset($imgPath) }}" alt="Service Image" class="img-fluid rounded" style="width: 150px; height: 150px; object-fit: cover;">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 