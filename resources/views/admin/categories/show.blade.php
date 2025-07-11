@extends('admin.layouts.app')

@section('title', 'تفاصيل التصنيف - لوحة تحكم لين')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">تفاصيل التصنيف: {{ $category->name }}</h1>
        <div>
            <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-primary">
                <i class="bi bi-pencil"></i> تعديل التصنيف
            </a>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-right"></i> العودة للتصنيفات
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">معلومات التصنيف</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        @if($category->image)
                            <img src="{{ asset($category->image) }}" alt="{{ $category->name }}" class="img-fluid rounded" style="max-height: 200px;">
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
                            <td>{{ $category->name }}</td>
                        </tr>
                        <tr>
                            <th>الرابط المختصر</th>
                            <td>{{ $category->slug }}</td>
                        </tr>
                        <tr>
                            <th>تاريخ الإنشاء</th>
                            <td>{{ $category->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                        <tr>
                            <th>آخر تحديث</th>
                            <td>{{ $category->updated_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    </table>
                    
                    <div class="mt-3">
                        <h6 class="font-weight-bold">الوصف:</h6>
                        <p>{{ $category->description ?: 'لا يوجد وصف' }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">التصنيفات الفرعية</h6>
                    <a href="{{ route('admin.subcategories.create') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-lg"></i> إضافة تصنيف فرعي
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الصورة</th>
                                    <th>الاسم</th>
                                    <th>الوصف</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($category->subCategories as $subCategory)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            @if($subCategory->image)
                                                <img src="{{ asset($subCategory->image) }}" alt="{{ $subCategory->name }}" width="50" height="50" class="img-thumbnail">
                                            @else
                                                <span class="badge bg-secondary">لا يوجد صورة</span>
                                            @endif
                                        </td>
                                        <td>{{ $subCategory->name }}</td>
                                        <td>{{ Str::limit($subCategory->description, 50) }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.subcategories.show', $subCategory->id) }}" class="btn btn-sm btn-info">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.subcategories.edit', $subCategory->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteSubModal{{ $subCategory->id }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>

                                            <!-- Delete Modal -->
                                            <div class="modal fade" id="deleteSubModal{{ $subCategory->id }}" tabindex="-1" aria-labelledby="deleteSubModalLabel{{ $subCategory->id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="deleteSubModalLabel{{ $subCategory->id }}">تأكيد الحذف</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            هل أنت متأكد من حذف التصنيف الفرعي "{{ $subCategory->name }}"؟
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                            <form action="{{ route('admin.subcategories.destroy', $subCategory->id) }}" method="POST" style="display: inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger">حذف</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">لا توجد تصنيفات فرعية</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 