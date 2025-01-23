<a class="@if($dropdownItem) dropdown-item @else btn @endif btn-{{ $color }} btn-{{ $size }} {{ $margin }}"
   href="{{ $href }}"
   id="{{ $id }}">
    <span class="material-icons mr-2">{{ $icon }}</span> {{ $text }}
</a>
