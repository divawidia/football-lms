<div class="form-group">
    <label class="form-label" for="{{ $name }}">{{ $label }}</label>
    @if($required)
        <small class="text-danger">*</small>
    @else
        <small class="text-muted">(Optional)</small>
    @endif
    <textarea class="form-control h-100 @if(!$modal) @error($name) is-invalid @enderror @endif" id="{{ $name }}" name="{{ $name }}" rows="{{ $row }}" placeholder="{{ $placeholder }}" @if($required) required @endif>@if(!$modal){{ old($name, $value) }}@endif</textarea>
    @if($modal)
        <span class="invalid-feedback {{ $name }}_error" role="alert"><strong></strong></span>
    @else
        @error($name)
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    @endif
</div>
