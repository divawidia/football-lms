<button type="{{ $type }}"
        id="{{ $id }}"
        @if($modalCloseIcon)
            class="close" aria-label="Close"
        @else
            class="@if($dropdownItem) dropdown-item @else btn @endif btn-{{ $color }} btn-{{ $size }} {{ $margin }} {{ $additionalClass }}"
        @endif
        @if($modalDismiss) data-bs-dismiss="modal" @endif>
    @if($modalCloseIcon)
        <span aria-hidden="true">&times;</span>
    @else
        <span class="material-icons text-{{ $iconColor }} mr-2">{{ $icon }}</span>
        {{ $text }}
    @endif
</button>
