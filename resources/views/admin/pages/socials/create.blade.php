@extends('admin.layouts.sidebar')

@section('title', 'Thêm mạng xã hội')

@section('main-content')
<div class="category-form-container">
    <!-- Breadcrumb -->
    <div class="content-breadcrumb">
        <ol class="breadcrumb-list">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.socials.index') }}">Mạng xã hội</a></li>
            <li class="breadcrumb-item current">Thêm mạng xã hội</li>
        </ol>
    </div>

    <div class="form-card">
        <div class="form-header">
            <div class="form-title">
                <i class="fas fa-plus-circle icon-title"></i>
                <h5>Thêm mạng xã hội mới</h5>
            </div>
        </div>
        <div class="form-body">
            <form action="{{ route('admin.socials.store') }}" method="POST" class="category-form" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="name" class="form-label-custom">
                        Tên mạng xã hội <span class="required-mark">*</span>
                    </label>
                    <input type="text" id="name" name="name" class="custom-input @error('name') input-error @enderror" 
                        placeholder="Ví dụ: Facebook" value="{{ old('name') }}">
                    @error('name')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="link" class="form-label-custom">
                        Đường dẫn <span class="required-mark">*</span>
                    </label>
                    <input type="text" id="link" name="link" class="custom-input @error('link') input-error @enderror" 
                        placeholder="Ví dụ: https://www.facebook.com/tencuaban" value="{{ old('link') }}">
                    @error('link')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                    <div class="form-hint">
                        <i class="fas fa-info-circle"></i> Đường dẫn phải bắt đầu bằng http:// hoặc https://
                    </div>
                </div>

                <div class="form-group">
                    <label for="icon" class="form-label-custom">
                        Icon SVG <span class="required-mark">*</span>
                    </label>
                    <div class="svg-upload-container">
                        <input type="file" id="icon" name="icon" class="file-input @error('icon') input-error @enderror" 
                               accept=".svg" onchange="previewSVG(this)">
                        <label for="icon" class="file-label">
                            <span class="file-icon"><i class="fas fa-upload"></i></span>
                            <span class="file-text">Chọn file SVG</span>
                        </label>
                        <div id="selected-file" class="selected-file">Chưa chọn file nào</div>
                    </div>
                    <div class="svg-preview-container" id="svg-preview-container">
                        <div class="svg-preview" id="svg-preview"></div>
                    </div>
                    @error('icon')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                    <div class="form-hint">
                        <i class="fas fa-info-circle"></i> Chỉ chấp nhận file SVG, kích thước tối đa 100KB
                    </div>
                </div>
                
                <div class="form-actions">
                    <a href="{{ route('admin.socials.index') }}" class="back-button">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                    <button type="submit" class="save-button">
                        <i class="fas fa-save"></i> Lưu lại
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .svg-upload-container {
        display: flex;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
        margin-bottom: 10px;
    }
    
    .file-input {
        width: 0.1px;
        height: 0.1px;
        opacity: 0;
        overflow: hidden;
        position: absolute;
        z-index: -1;
    }
    
    .file-label {
        display: inline-flex;
        align-items: center;
        padding: 10px 15px;
        background-color: #f3f4f6;
        color: #374151;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s;
        border: 1px solid #d1d5db;
    }
    
    .file-label:hover {
        background-color: #e5e7eb;
    }
    
    .file-icon {
        margin-right: 8px;
    }
    
    .selected-file {
        margin-left: 10px;
        font-size: 14px;
        color: #6b7280;
    }
    
    .svg-preview-container {
        display: none;
        margin-top: 15px;
    }
    
    .svg-preview {
        width: 100px;
        height: 100px;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 15px;
        background-color: #f3f4f6;
    }
    
    .svg-preview img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
</style>
@endpush

@push('scripts')
<script>
    function previewSVG(input) {
        const fileInfo = document.getElementById('selected-file');
        const previewContainer = document.getElementById('svg-preview-container');
        const previewDiv = document.getElementById('svg-preview');
        
        if (input.files && input.files[0]) {
            const file = input.files[0];
            
            // Check if file is SVG
            if (file.type !== 'image/svg+xml') {
                fileInfo.textContent = 'Lỗi: Vui lòng chọn file SVG';
                fileInfo.style.color = '#dc3545';
                previewContainer.style.display = 'none';
                return;
            }
            
            // Check file size (max 100KB)
            if (file.size > 100 * 1024) {
                fileInfo.textContent = 'Lỗi: File quá lớn (tối đa 100KB)';
                fileInfo.style.color = '#dc3545';
                previewContainer.style.display = 'none';
                return;
            }
            
            // Update file info
            fileInfo.textContent = `${file.name} (${formatFileSize(file.size)})`;
            fileInfo.style.color = '#6b7280';
            
            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                previewDiv.innerHTML = `<img src="${e.target.result}" alt="SVG Preview">`;
                previewContainer.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            fileInfo.textContent = 'Chưa chọn file nào';
            previewContainer.style.display = 'none';
        }
    }
    
    function formatFileSize(bytes) {
        if (bytes < 1024) {
            return bytes + ' bytes';
        } else if (bytes < 1024 * 1024) {
            return (bytes / 1024).toFixed(1) + ' KB';
        } else {
            return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
        }
    }
</script>
@endpush