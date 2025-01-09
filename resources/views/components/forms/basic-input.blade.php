<div class="@if($type == 'file') custom-file @else form-group @endif">
    @if($type != 'file' && $label)
        <label class="form-label" for="{{ $name }}">{{ $label }}</label>
        @if($required)
            <small class="text-danger">*</small>
        @else
            <small class="text-muted">(Optional)</small>
        @endif
    @endif

    <input type="{{ $type }}"
           id="{{ $name }}"
           name="{{ $name }}"
            @if($required) required @endif
           class="@if($type == 'file') custom-file-input @else form-control @endif @if(!$modal) @error($name) is-invalid @enderror @endif"
           placeholder="{{ $placeholder }}"
            @if($type == 'file') accept="{{ $acceptFileType }}" @endif
           @if($type == 'number') min="{{ $min }}" max="{{ $max }}" @endif
           @if($modal) value="{{ old($name, $value) }}" @endif>

    @if($type == 'file')
        <label class="custom-file-label" for="{{ $name }}">{{ $label }}</label>
        @endif
    @if($modal)
        <span class="invalid-feedback {{ $name }}_error" role="alert"><strong></strong></span>
    @else
        @error($name)
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    @endif

</div>
