<button type="{{ $type }}"
        id="{{ $id }}"
        @if($modalCloseIcon)
            class="close" aria-label="Close"
        @else
            class="btn btn-{{ $color }} btn-{{ $size }} {{ $margin }}"
        @endif
        @if($modalDismiss) data-bs-dismiss="modal" @endif>
    @if($modalCloseIcon)
        <span aria-hidden="true">&times;</span>
    @else
        <span class="material-icons mr-2">{{ $icon }}</span>
        {{ $text }}
    @endif
</button>
