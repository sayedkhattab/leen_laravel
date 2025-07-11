@extends('admin.layouts.app')

@section('title', 'تعديل العميل - لوحة تحكم لين')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">تعديل العميل: {{ $customer->first_name }} {{ $customer->last_name }}</h1>
        <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-right"></i> العودة للعملاء
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">بيانات العميل</h6>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.customers.update', $customer->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-primary">المعلومات الأساسية</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="first_name" class="form-label">الاسم الأول <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                                        </div>
                                        <input type="text" name="first_name" id="first_name" class="form-control" value="{{ old('first_name', $customer->first_name) }}" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="last_name" class="form-label">الاسم الأخير <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                                        </div>
                                        <input type="text" name="last_name" id="last_name" class="form-control" value="{{ old('last_name', $customer->last_name) }}" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                                        </div>
                                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $customer->email) }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-primary">معلومات إضافية</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                                        </div>
                                        <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $customer->phone) }}" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="password" class="form-label">كلمة المرور (اتركها فارغة إذا لم ترغب في تغييرها)</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                        </div>
                                        <input type="password" name="password" id="password" class="form-control">
                                    </div>
                                    <small class="form-text text-muted">يجب أن تكون كلمة المرور 8 أحرف على الأقل</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="location" class="form-label">الموقع</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="bi bi-geo-alt-fill"></i></span>
                                        </div>
                                        <textarea name="location" id="location" class="form-control" rows="3">{{ old('location', $customer->location) }}</textarea>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="status" class="form-label">الحالة</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="active" {{ $customer->status == 'active' ? 'selected' : '' }}>نشط</option>
                                        <option value="inactive" {{ $customer->status == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">الصورة الشخصية</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="image" class="form-label">الصورة</label>
                                    <input type="file" class="form-control" id="image" name="image">
                                    <small class="form-text text-muted">الصيغ المسموحة: jpeg, png, jpg, gif. الحد الأقصى للحجم: 2 ميجابايت</small>
                                </div>
                                
                                <div class="mt-3" id="imagePreview" style="display: none;">
                                    <p>معاينة الصورة الجديدة:</p>
                                    <img src="" alt="معاينة الصورة" class="img-thumbnail" style="max-height: 200px;">
                                </div>
                            </div>
                            <div class="col-md-4">
                                @if ($customer->image)
                                    <div class="text-center">
                                        <p class="text-muted">الصورة الحالية:</p>
                                        <img src="{{ asset($customer->image) }}" alt="{{ $customer->first_name }}" class="img-thumbnail" style="max-height: 200px;">
                                    </div>
                                @else
                                    <div class="text-center">
                                        <p class="text-muted">لا توجد صورة حالية</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> حفظ التغييرات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Image preview
        $('#image').on('change', function() {
            var file = this.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview img').attr('src', e.target.result);
                    $('#imagePreview').show();
                }
                reader.readAsDataURL(file);
            }
        });
    });
</script>
@endpush 