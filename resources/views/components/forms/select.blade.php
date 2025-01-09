<div class="form-group">
    @if($label)
        <label class="form-label" for="{{ $id }}">{{ $label }}</label>
        @if($required)
            <small class="text-danger">*</small>
        @else
            <small class="text-muted">(Optional)</small>
        @endif
    @endif
    <select class="form-control form-select @if(!$modal) @error($name) is-invalid @enderror @endif"
            id="{{ $id }}"
            name="{{ $name }}"
            @if($required) required @endif
            @if($select2) data-toggle="select" @endif
            @if($multiple) multiple @endif>
        {{ $slot }}
    </select>
    @if($modal)
        <span class="invalid-feedback {{ $name }}_error" role="alert"><strong></strong></span>
    @else
        @error($name)
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    @endif
</div>
