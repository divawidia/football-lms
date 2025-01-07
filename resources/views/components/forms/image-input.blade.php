<label class="form-label">{{ $label }}</label>
@if($required)
    <small class="text-danger">*</small>
@else
    <small class="text-muted">(Optional)</small>
@endif
<div class="media align-items-center mb-2">
    <img src="{{ Storage::url('images/undefined-user.png') }}"
         alt="img"
         width="54"
         height="54"
         class="mr-16pt rounded-circle img-object-fit-cover"
        id="preview"/>
    <div class="media-body">
        <x-forms.basic-input type="file" :name="$name" acceptFileType="image/jpg, image/jpeg, image/png" :required="$required" :modal="$modal" label="Choose Image"/>
    </div>
</div>
