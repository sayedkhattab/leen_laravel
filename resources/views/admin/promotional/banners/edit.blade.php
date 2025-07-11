@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header" style="background-color: #2F3E3B; color: white;">
                    <h3 class="card-title">تعديل البانر الإعلاني</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.promotional.banners.index') }}" class="btn btn-light">
                            <i class="bi bi-arrow-right"></i> العودة للقائمة
                        </a>
                    </div>
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

                    <form action="{{ route('admin.promotional.banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="title" class="form-label">العنوان <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $banner->title) }}" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="subtitle" class="form-label">العنوان الفرعي</label>
                                <input type="text" class="form-control" id="subtitle" name="subtitle" value="{{ old('subtitle', $banner->subtitle) }}">
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="image" class="form-label">صورة البانر</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                <small class="text-muted">يفضل أن تكون الصورة بأبعاد 1200×400 بكسل</small>
                                
                                @if($banner->image_path)
                                <div class="mt-2">
                                    <p>الصورة الحالية:</p>
                                    <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="img-thumbnail" style="max-width: 300px;">
                                </div>
                                @endif
                            </div>
                            
                            <div class="col-md-6">
                                <label for="target_audience" class="form-label">الجمهور المستهدف <span class="text-danger">*</span></label>
                                <select class="form-select" id="target_audience" name="target_audience" required>
                                    <option value="all" {{ old('target_audience', $banner->target_audience) == 'all' ? 'selected' : '' }}>الجميع</option>
                                    <option value="customers" {{ old('target_audience', $banner->target_audience) == 'customers' ? 'selected' : '' }}>العملاء فقط</option>
                                    <option value="sellers" {{ old('target_audience', $banner->target_audience) == 'sellers' ? 'selected' : '' }}>مقدمي الخدمات فقط</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="action_text" class="form-label">نص زر الإجراء</label>
                                <input type="text" class="form-control" id="action_text" name="action_text" value="{{ old('action_text', $banner->action_text) }}">
                                <small class="text-muted">مثال: احجز الآن، اشترك، تعرف على المزيد</small>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="link_type" class="form-label">نوع الرابط <span class="text-danger">*</span></label>
                                <select class="form-select" id="link_type" name="link_type" required>
                                    <option value="url" {{ old('link_type', $banner->link_type) == 'url' ? 'selected' : '' }}>رابط خارجي</option>
                                    <option value="seller" {{ old('link_type', $banner->link_type) == 'seller' ? 'selected' : '' }}>مقدم خدمة</option>
                                    <option value="home_service" {{ old('link_type', $banner->link_type) == 'home_service' ? 'selected' : '' }}>خدمة منزلية</option>
                                    <option value="studio_service" {{ old('link_type', $banner->link_type) == 'studio_service' ? 'selected' : '' }}>خدمة في الاستوديو</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- حقول الروابط المختلفة -->
                        <div class="row mb-3 link-fields" id="url-fields">
                            <div class="col-md-12">
                                <label for="action_url" class="form-label">رابط زر الإجراء</label>
                                <input type="text" class="form-control" id="action_url" name="action_url" value="{{ old('action_url', $banner->action_url) }}">
                            </div>
                        </div>
                        
                        <div class="row mb-3 link-fields" id="seller-fields" style="display: none;">
                            <div class="col-md-12">
                                <label for="linked_seller_id" class="form-label">اختر مقدم خدمة</label>
                                <select class="form-select" id="linked_seller_id" name="linked_seller_id">
                                    <option value="">-- اختر مقدم خدمة --</option>
                                    @foreach(\App\Models\Seller::where('status', 'active')->orderBy('first_name')->get() as $seller)
                                        <option value="{{ $seller->id }}" {{ old('linked_seller_id', $banner->linked_seller_id) == $seller->id ? 'selected' : '' }}>
                                            {{ $seller->first_name }} {{ $seller->last_name }} ({{ $seller->phone }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-3 link-fields" id="home-service-fields" style="display: none;">
                            <div class="col-md-12">
                                <label for="linked_home_service_id" class="form-label">اختر خدمة منزلية</label>
                                <select class="form-select" id="linked_home_service_id" name="linked_home_service_id">
                                    <option value="">-- اختر خدمة منزلية --</option>
                                    @foreach(\App\Models\HomeService::with('seller')->orderBy('name')->get() as $service)
                                        <option value="{{ $service->id }}" {{ old('linked_home_service_id', $banner->linked_home_service_id) == $service->id ? 'selected' : '' }}>
                                            {{ $service->name }} - {{ $service->seller->name ?? 'غير معروف' }} ({{ $service->price }} ريال)
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-3 link-fields" id="studio-service-fields" style="display: none;">
                            <div class="col-md-12">
                                <label for="linked_studio_service_id" class="form-label">اختر خدمة استوديو</label>
                                <select class="form-select" id="linked_studio_service_id" name="linked_studio_service_id">
                                    <option value="">-- اختر خدمة استوديو --</option>
                                    @foreach(\App\Models\StudioService::with('seller')->orderBy('name')->get() as $service)
                                        <option value="{{ $service->id }}" {{ old('linked_studio_service_id', $banner->linked_studio_service_id) == $service->id ? 'selected' : '' }}>
                                            {{ $service->name }} - {{ $service->seller->name ?? 'غير معروف' }} ({{ $service->price }} ريال)
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="display_order" class="form-label">ترتيب العرض</label>
                                <input type="number" class="form-control" id="display_order" name="display_order" value="{{ old('display_order', $banner->display_order) }}" min="0">
                                <small class="text-muted">الأرقام الأصغر تظهر أولاً</small>
                            </div>
                            
                            <div class="col-md-6 mt-4">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="is_active" value="0">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $banner->is_active) == '1' || old('is_active', $banner->is_active) === true ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">نشط</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="is_limited_time" value="0">
                                    <input class="form-check-input" type="checkbox" id="is_limited_time" name="is_limited_time" value="1" {{ old('is_limited_time', $banner->is_limited_time) == '1' || old('is_limited_time', $banner->is_limited_time) === true ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_limited_time">عرض لفترة محدودة</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3 limited-time-fields" style="display: {{ old('is_limited_time', $banner->is_limited_time) ? 'flex' : 'none' }};">
                            <div class="col-md-6">
                                <label for="starts_at" class="form-label">تاريخ البدء</label>
                                <input type="datetime-local" class="form-control" id="starts_at" name="starts_at" value="{{ old('starts_at', $banner->starts_at ? $banner->starts_at->format('Y-m-d\TH:i') : '') }}">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="expires_at" class="form-label">تاريخ الانتهاء</label>
                                <input type="datetime-local" class="form-control" id="expires_at" name="expires_at" value="{{ old('expires_at', $banner->expires_at ? $banner->expires_at->format('Y-m-d\TH:i') : '') }}">
                            </div>
                        </div>
                        
                        <div class="text-center mt-4">
                            <button type="submit" class="btn" style="background-color: #2F3E3B; color: white;">
                                <i class="bi bi-save"></i> حفظ التعديلات
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .search-results {
        max-height: 200px;
        overflow-y: auto;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 10px;
        display: none;
    }
    
    .search-item {
        padding: 8px;
        border-bottom: 1px solid #eee;
        cursor: pointer;
    }
    
    .search-item:hover {
        background-color: #f5f5f5;
    }
    
    .search-item:last-child {
        border-bottom: none;
    }
    
    .selected-item-container {
        margin-top: 10px;
    }
    
    .select2-container--default .select2-selection--single {
        height: 38px;
        padding: 5px;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // الحصول على العناصر من DOM
        const isLimitedTimeCheckbox = document.getElementById('is_limited_time');
        const limitedTimeFields = document.querySelector('.limited-time-fields');
        const linkTypeSelect = document.getElementById('link_type');
        
        // تحديد جميع حقول الروابط
        const urlFields = document.getElementById('url-fields');
        const sellerFields = document.getElementById('seller-fields');
        const homeServiceFields = document.getElementById('home-service-fields');
        const studioServiceFields = document.getElementById('studio-service-fields');
        
        // تهيئة Select2 للقوائم المنسدلة
        $('#linked_seller_id').select2({
            placeholder: "-- اختر مقدم خدمة --",
            allowClear: true,
            width: '100%',
            dir: 'rtl',
            language: 'ar'
        });
        
        $('#linked_home_service_id').select2({
            placeholder: "-- اختر خدمة منزلية --",
            allowClear: true,
            width: '100%',
            dir: 'rtl',
            language: 'ar'
        });
        
        $('#linked_studio_service_id').select2({
            placeholder: "-- اختر خدمة استوديو --",
            allowClear: true,
            width: '100%',
            dir: 'rtl',
            language: 'ar'
        });
        
        // دالة لإظهار حقول الرابط المناسبة وإخفاء الباقي
        function showLinkFields(linkType) {
            // إخفاء جميع الحقول أولاً
            urlFields.style.display = 'none';
            sellerFields.style.display = 'none';
            homeServiceFields.style.display = 'none';
            studioServiceFields.style.display = 'none';
            
            const refreshSelect2 = (selector) => {
                $(selector).next('.select2-container').css('width', '100%');
            };
            // إظهار الحقل المناسب حسب نوع الرابط
            switch(linkType) {
                case 'url':
                    urlFields.style.display = 'flex';
                    break;
                case 'seller':
                    sellerFields.style.display = 'flex';
                    refreshSelect2('#linked_seller_id');
                    break;
                case 'home_service':
                    homeServiceFields.style.display = 'flex';
                    refreshSelect2('#linked_home_service_id');
                    break;
                case 'studio_service':
                    studioServiceFields.style.display = 'flex';
                    refreshSelect2('#linked_studio_service_id');
                    break;
            }
        }
        
        // تعيين الحالة الأولية لحقول الرابط
        showLinkFields(linkTypeSelect.value);
        
        // الاستماع لتغييرات نوع الرابط
        linkTypeSelect.addEventListener('change', function() {
            showLinkFields(this.value);
        });
        
        // الحالة الأولية لحقول الفترة المحدودة
        if (isLimitedTimeCheckbox.checked) {
            limitedTimeFields.style.display = 'flex';
        }
        
        // الاستماع لتغييرات خيار الفترة المحدودة
        isLimitedTimeCheckbox.addEventListener('change', function() {
            if (this.checked) {
                limitedTimeFields.style.display = 'flex';
            } else {
                limitedTimeFields.style.display = 'none';
            }
        });
    });
</script>
@endsection 