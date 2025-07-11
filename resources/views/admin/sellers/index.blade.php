@extends('admin.layouts.app')

@section('title', 'مقدمي الخدمات - لوحة تحكم لين')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">مقدمي الخدمات</h1>
        <a href="{{ route('admin.sellers.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> إضافة مقدم خدمة جديد
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">جميع مقدمي الخدمات</h6>
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
                            <th>الاسم</th>
                            <th>البريد الإلكتروني</th>
                            <th class="text-center">رقم الهاتف</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sellers as $seller)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    @if ($seller->seller_logo)
                                        <img src="{{ asset($seller->seller_logo) }}" alt="{{ $seller->first_name }}" width="50" height="50" class="img-thumbnail">
                                    @else
                                        <img src="{{ asset('images/logo/green_logo.png') }}" alt="Default" width="50" height="50" class="img-thumbnail">
                                    @endif
                                </td>
                                <td>{{ $seller->first_name }} {{ $seller->last_name }}</td>
                                <td>{{ $seller->email }}</td>
                                <td class="text-center">{{ $seller->phone }}</td>
                                <td>
                                    @if ($seller->request_status === 'pending')
                                        <span class="badge bg-warning">قيد الانتظار</span>
                                    @elseif ($seller->request_status === 'approved')
                                        <span class="badge bg-success">تمت الموافقة</span>
                                    @elseif ($seller->request_status === 'rejected')
                                        <span class="badge bg-danger">مرفوض</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap">
                                        <a href="{{ route('admin.sellers.show', $seller->id) }}" class="btn btn-sm btn-info action-btn">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.sellers.edit', $seller->id) }}" class="btn btn-sm btn-primary action-btn">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @if ($seller->request_status === 'pending')
                                            <form action="{{ route('admin.sellers.approve', $seller->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success action-btn">
                                                    <i class="bi bi-check"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.sellers.reject', $seller->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger action-btn">
                                                    <i class="bi bi-x"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <button type="button" class="btn btn-sm btn-danger action-btn" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $seller->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>

                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteModal{{ $seller->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $seller->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel{{ $seller->id }}">تأكيد الحذف</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    هل أنت متأكد من حذف مقدم الخدمة "{{ $seller->first_name }} {{ $seller->last_name }}"؟
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                    <form action="{{ route('admin.sellers.destroy', $seller->id) }}" method="POST" style="display: inline;">
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
                                <td colspan="7" class="text-center">لا يوجد مقدمي خدمات</td>
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
    margin:0 3px 3px 0; /* مساحة بين الأزرار */
    border-radius:6px !important; /* حواف دائرية كاملة */
}
</style>
@endsection 