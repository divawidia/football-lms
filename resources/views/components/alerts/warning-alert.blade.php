<div class="alert alert-light border-left-accent" role="alert">
    <div class="d-flex flex-wrap align-items-center">
        <i class="material-icons mr-8pt">error_outline</i>
        <div class="media-body" style="min-width: 180px">
            <small class="text-black-100">{{ $text }}</small>
        </div>
        @if($createRoute)
            <div class="ml-8pt mt-2 mt-sm-0">
                <a href="{{ $createRoute }}"
                   class="btn btn-link btn-sm">Create Now</a>
            </div>
        @endif
    </div>
</div>
