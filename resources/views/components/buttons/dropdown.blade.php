<div class="dropdown">
    <button class="btn btn-{{ $btnColor }}" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        {{ $title }}
        <span class="material-icons {{ $iconMargin }}">{{ $icon }}</span>
    </button>
    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        {{ $slot }}
    </div>
</div>
