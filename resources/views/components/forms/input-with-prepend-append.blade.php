<div class="form-group">
    <label class="form-label" for="{{ $name }}">{{ $label }}</label>
    @if($required)
        <small class="text-danger">*</small>
    @else
        <small class="text-muted">(Optional)</small>
    @endif
    <div class="input-group input-group-merge">
        <input type="{{ $type }}"
               id="{{ $name }}"
               name="{{ $name }}"
               class="form-control @if($append) form-control-appended @else form-control-prepended @endif @if(!$modal) @error($name) is-invalid @enderror @endif"
               placeholder="{{ $placeholder }}"
               @if($required) required @endif
               @if($type == 'number') min="{{ $min }}" max="{{ $max }}" @endif
               @if(!$modal)value="{{ old($name, $value) }}"@endif>

        <div class="@if($append) input-group-append @else input-group-prepend @endif">
            <div class="input-group-text">
                @if($icon) <span class="material-icons">{{ $icon }}</span> @endif {{ $text }}
            </div>
        </div>
    </div>

    @if($modal)
        <span class="invalid-feedback {{ $name }}_error" role="alert"><strong></strong></span>
    @else
        @error($name)
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    @endif
</div>
