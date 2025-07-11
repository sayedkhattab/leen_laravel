@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title">إضافة خدمة مميزة</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.promotional.featured-services.index') }}" class="btn btn-light">
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

                    <form action="{{ route('admin.promotional.featured-services.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="form-label">نوع الخدمة <span class="text-danger">*</span></label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="service_type" id="service_type_home" value="home_service" {{ old('service_type') == 'home_service' ? 'checked' : '' }} checked>
                                <label class="form-check-label" for="service_type_home">
                                    خدمة منزلية
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="service_type" id="service_type_studio" value="studio_service" {{ old('service_type') == 'studio_service' ? 'checked' : '' }}>
                                <label class="form-check-label" for="service_type_studio">
                                    خدمة استوديو
                                </label>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="service_id" class="form-label">اختر الخدمة <span class="text-danger">*</span></label>
                                <select class="form-select" id="service_id" name="service_id" required>
                                    <option value="">-- اختر الخدمة --</option>
                                    <optgroup label="الخدمات المنزلية" id="home_services_group">
                                        @foreach($homeServices as $service)
                                            <option value="{{ $service->id }}" data-type="home_service" {{ old('service_id') == $service->id && old('service_type') == 'home_service' ? 'selected' : '' }}>
                                                {{ $service->name }} - {{ $service->seller->first_name }} {{ $service->seller->last_name }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="خدمات الاستوديو" id="studio_services_group">
                                        @foreach($studioServices as $service)
                                            <option value="{{ $service->id }}" data-type="studio_service" {{ old('service_id') == $service->id && old('service_type') == 'studio_service' ? 'selected' : '' }}>
                                                {{ $service->name }} - {{ $service->seller->first_name }} {{ $service->seller->last_name }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="display_order" class="form-label">ترتيب العرض</label>
                                <input type="number" class="form-control" id="display_order" name="display_order" value="{{ old('display_order', 0) }}" min="0">
                                <small class="text-muted">الأرقام الأصغر تظهر أولاً</small>
                            </div>
                            
                            <div class="col-md-6 mt-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">نشط</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="starts_at" class="form-label">تاريخ البدء</label>
                                <input type="datetime-local" class="form-control" id="starts_at" name="starts_at" value="{{ old('starts_at') }}">
                                <small class="text-muted">اتركه فارغاً للعرض فوراً</small>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="expires_at" class="form-label">تاريخ الانتهاء</label>
                                <input type="datetime-local" class="form-control" id="expires_at" name="expires_at" value="{{ old('expires_at') }}">
                                <small class="text-muted">اتركه فارغاً لعدم وجود تاريخ انتهاء</small>
                            </div>
                        </div>
                        
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary px-5">
                                <i class="bi bi-save"></i> حفظ
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
    document.addEventListener('DOMContentLoaded', function() {
        const serviceTypeHomeRadio = document.getElementById('service_type_home');
        const serviceTypeStudioRadio = document.getElementById('service_type_studio');
        const serviceSelect = document.getElementById('service_id');
        const homeServicesGroup = document.getElementById('home_services_group');
        const studioServicesGroup = document.getElementById('studio_services_group');
        
        // Function to filter options based on service type
        function filterServiceOptions() {
            const serviceType = document.querySelector('input[name="service_type"]:checked').value;
            
            // Reset selection
            serviceSelect.value = '';
            
            // Show/hide option groups
            if (serviceType === 'home_service') {
                homeServicesGroup.style.display = '';
                studioServicesGroup.style.display = 'none';
            } else {
                homeServicesGroup.style.display = 'none';
                studioServicesGroup.style.display = '';
            }
        }
        
        // Initial filter
        filterServiceOptions();
        
        // Add event listeners
        serviceTypeHomeRadio.addEventListener('change', filterServiceOptions);
        serviceTypeStudioRadio.addEventListener('change', filterServiceOptions);
        
        // Sync service type with selected service
        serviceSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                const serviceType = selectedOption.getAttribute('data-type');
                if (serviceType === 'home_service') {
                    serviceTypeHomeRadio.checked = true;
                } else {
                    serviceTypeStudioRadio.checked = true;
                }
            }
        });
    });
</script>
@endpush 