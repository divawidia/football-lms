<div class="form-group">
    @if($label)
        <label class="form-label" for="{{ $id }}">{{ $label }}</label>
        @if($required)
            <small class="text-danger">*</small>
        @else
            <small class="text-muted">(Optional)</small>
        @endif
    @endif
    <input type="text"
           id="{{ $id }}"
           name="{{ $name }}"
           @if($required) required @endif
           class="form-control @if(!$modal) @error($name) is-invalid @enderror @endif"
           placeholder="{{ $placeholder }}"
           data-toggle="flatpickr"
           data-flatpickr-enable-time="true"
           data-flatpickr-no-calendar="true"
           data-flatpickr-alt-format="H:i"
           data-flatpickr-date-format="H:i">

    @if($modal)
        <span class="invalid-feedback {{ $name }}_error" role="alert"><strong></strong></span>
    @else
        @error($name)
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    @endif
</div>
