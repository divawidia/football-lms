<div class="form-group">
    <label class="form-label" for="{{ $id }}">{{ $label }}</label>
    @if($required)
        <small class="text-danger">*</small>
    @else
        <small class="text-muted">(Optional)</small>
    @endif
    <div class="media justify-content-center mb-2">
        <div class="custom-file">
            <input type="file"
                   class="custom-file-input @if(!$modal) @error($name) is-invalid @enderror @endif"
                   name="{{ $name }}"
                   id="{{ $id }}"
                    @if($required)
                        required
                    @endif
                   accept="image/jpg, image/jpeg, image/png">
            <label class="custom-file-label" for="{{ $id }}">Choose image</label>
            @if($modal)
                <span class="invalid-feedback {{ $name }}_error" role="alert"><strong></strong></span>
            @else
                @error($name)
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            @endif
        </div>
        <img id="preview" class="image-upload-preview img-fluid mt-4" alt="image-preview" @if(!$modal) src="{{ Storage::url($value ?? '') }}" @endif>
    </div>
</div>
