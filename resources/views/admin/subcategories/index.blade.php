@extends('admin.layouts.app')

@section('title', 'إدارة التصنيفات الفرعية - لوحة تحكم لين')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">إدارة التصنيفات الفرعية</h1>
        <a href="{{ route('admin.subcategories.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> إضافة تصنيف فرعي جديد
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">جميع التصنيفات الفرعية</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الصورة</th>
                            <th>الاسم</th>
                            <th>التصنيف الرئيسي</th>
                            <th>الوصف</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subcategories as $subcategory)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    @if($subcategory->image)
                                        <img src="{{ asset($subcategory->image) }}" alt="{{ $subcategory->name }}" width="50" height="50" class="img-thumbnail">
                                    @else
                                        <span class="badge bg-secondary">لا يوجد صورة</span>
                                    @endif
                                </td>
                                <td>{{ $subcategory->name }}</td>
                                <td>
                                    <a href="{{ route('admin.categories.show', $subcategory->category_id) }}">
                                        {{ $subcategory->category->name }}
                                    </a>
                                </td>
                                <td>{{ Str::limit($subcategory->description, 50) }}</td>
                                <td>
                                    <div class="d-flex flex-wrap">
                                        <a href="{{ route('admin.subcategories.show', $subcategory->id) }}" class="btn btn-sm btn-info action-btn">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.subcategories.edit', $subcategory->id) }}" class="btn btn-sm btn-primary action-btn">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger action-btn" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $subcategory->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>

                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteModal{{ $subcategory->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $subcategory->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel{{ $subcategory->id }}">تأكيد الحذف</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    هل أنت متأكد من حذف التصنيف الفرعي "{{ $subcategory->name }}"؟
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                    <form action="{{ route('admin.subcategories.destroy', $subcategory->id) }}" method="POST" style="display: inline;">
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
                                <td colspan="6" class="text-center">لا توجد تصنيفات فرعية</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.action-btn{
    margin:0 3px 3px 0;
    border-radius:6px !important;
}
</style>
@endsection 