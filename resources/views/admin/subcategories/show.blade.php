@extends('admin.layouts.app')

@section('title', 'تفاصيل التصنيف الفرعي - لوحة تحكم لين')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">تفاصيل التصنيف الفرعي: {{ $subcategory->name }}</h1>
        <div>
            <a href="{{ route('admin.subcategories.edit', $subcategory->id) }}" class="btn btn-primary">
                <i class="bi bi-pencil"></i> تعديل التصنيف الفرعي
            </a>
            <a href="{{ route('admin.subcategories.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-right"></i> العودة للتصنيفات الفرعية
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">معلومات التصنيف الفرعي</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        @if($subcategory->image)
                            <img src="{{ asset($subcategory->image) }}" alt="{{ $subcategory->name }}" class="img-fluid rounded" style="max-height: 200px;">
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
                            <td>{{ $subcategory->name }}</td>
                        </tr>
                        <tr>
                            <th>التصنيف الرئيسي</th>
                            <td>
                                <a href="{{ route('admin.categories.show', $subcategory->category_id) }}">
                                    {{ $subcategory->category->name }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>الرابط المختصر</th>
                            <td>{{ $subcategory->slug }}</td>
                        </tr>
                        <tr>
                            <th>تاريخ الإنشاء</th>
                            <td>{{ $subcategory->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                        <tr>
                            <th>آخر تحديث</th>
                            <td>{{ $subcategory->updated_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    </table>
                    
                    <div class="mt-3">
                        <h6 class="font-weight-bold">الوصف:</h6>
                        <p>{{ $subcategory->description ?: 'لا يوجد وصف' }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">الخدمات المرتبطة</h6>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs" id="servicesTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="home-services-tab" data-bs-toggle="tab" data-bs-target="#home-services" type="button" role="tab" aria-controls="home-services" aria-selected="true">خدمات منزلية</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="studio-services-tab" data-bs-toggle="tab" data-bs-target="#studio-services" type="button" role="tab" aria-controls="studio-services" aria-selected="false">خدمات استوديو</button>
                        </li>
                    </ul>
                    
                    <div class="tab-content pt-3" id="servicesTabContent">
                        <div class="tab-pane fade show active" id="home-services" role="tabpanel" aria-labelledby="home-services-tab">
                            @if($subcategory->homeServices->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>الاسم</th>
                                                <th>السعر</th>
                                                <th>مقدم الخدمة</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($subcategory->homeServices as $service)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $service->name }}</td>
                                                    <td>{{ $service->price }} ريال</td>
                                                    <td>
                                                        <a href="{{ route('admin.sellers.show', $service->seller_id) }}">
                                                            {{ $service->seller->name }}
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    لا توجد خدمات منزلية مرتبطة بهذا التصنيف الفرعي
                                </div>
                            @endif
                        </div>
                        
                        <div class="tab-pane fade" id="studio-services" role="tabpanel" aria-labelledby="studio-services-tab">
                            @if($subcategory->studioServices->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>الاسم</th>
                                                <th>السعر</th>
                                                <th>مقدم الخدمة</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($subcategory->studioServices as $service)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $service->name }}</td>
                                                    <td>{{ $service->price }} ريال</td>
                                                    <td>
                                                        <a href="{{ route('admin.sellers.show', $service->seller_id) }}">
                                                            {{ $service->seller->name }}
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    لا توجد خدمات استوديو مرتبطة بهذا التصنيف الفرعي
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 