<div class="form-group">
    <div class="row d-flex flex-row align-items-center mb-2">
        <div class="col-md-3">
            <label class="form-label" for="{{ $name }}">{{ $label }}</label>
            @if($required)
                <small class="text-danger">*</small>
            @else
                <small class="text-muted">(Optional)</small>
            @endif
        </div>
        <div class="col-md-9">
            <input type="text" id="{{ $name }}" name="{{ $name }}" class="skills-range-slider" @if($required) required @endif/>
            @if($modal)
                <span class="invalid-feedback {{ $name }}_error" role="alert"><strong></strong></span>
            @else
                @error($name)
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            @endif
        </div>
    </div>
</div>
