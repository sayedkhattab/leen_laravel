@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-gradient-success">
                    <h3 class="card-title text-white">إضافة مقدم خدمة جديد</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.sellers.index') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left"></i> العودة
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h5><i class="icon fas fa-ban"></i> خطأ!</h5>
                            <ul class="list-unstyled">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.sellers.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card card-primary card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title">المعلومات الأساسية</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="name">الاسم <span class="text-danger">*</span></label>
                                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                                            <small class="form-text text-muted">أدخل الاسم الكامل لمقدم الخدمة</small>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="email">البريد الإلكتروني <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                </div>
                                                <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="phone">رقم الهاتف <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                </div>
                                                <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone') }}" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card card-primary card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title">معلومات إضافية</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="password">كلمة المرور <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                                </div>
                                                <input type="password" name="password" id="password" class="form-control" required>
                                            </div>
                                            <small class="form-text text-muted">يجب أن تكون كلمة المرور 8 أحرف على الأقل</small>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="address">العنوان</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                                </div>
                                                <textarea name="address" id="address" class="form-control" rows="3">{{ old('address') }}</textarea>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="status">الحالة</label>
                                            <select name="status" id="status" class="form-control">
                                                <option value="pending">قيد الانتظار</option>
                                                <option value="approved">تمت الموافقة</option>
                                                <option value="rejected">مرفوض</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">الصورة الشخصية</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="image">الصورة</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" name="image" id="image" class="custom-file-input">
                                            <label class="custom-file-label" for="image">اختر صورة</label>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">يفضل رفع صورة بأبعاد 300×300 بكسل</small>
                                </div>
                                
                                <div class="mt-3" id="imagePreview" style="display: none;">
                                    <img src="" alt="معاينة الصورة" class="img-thumbnail" style="max-width: 200px;">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group text-center mt-4">
                            <button type="submit" class="btn btn-success btn-lg px-5">
                                <i class="fas fa-plus-circle mr-1"></i> إضافة مقدم الخدمة
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Show file name when selected
        $('.custom-file-input').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
            
            // Image preview
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview img').attr('src', e.target.result);
                    $('#imagePreview').show();
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
</script>
@endpush 