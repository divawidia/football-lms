<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered {{ $size }}">
        <div class="modal-content">
            <form action="" method="POST" id="{{ $formId }}">
                @if($editForm) @method('PUT') @endif
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">{{ $title }}</h5>
                    <x-buttons.basic-button :modalCloseIcon="true" :modalDismiss="true"/>
                </div>
                <div class="modal-body">
                    {{ $slot }}
                </div>
                <div class="modal-footer">
                    <x-buttons.basic-button type="button" color="secondary" :modalDismiss="true" text="Cancel"/>
                    <x-buttons.basic-button type="submit" text="Submit"/>
                </div>
            </form>
        </div>
    </div>
</div>
