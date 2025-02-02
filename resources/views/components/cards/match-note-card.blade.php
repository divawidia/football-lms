<div class="card">
    <div class="card-header d-flex align-items-center">
        <div class="flex">
            <h4 class="card-title">{{ convertToDatetime($note->created_at) }}</h4>
            <div class="card-subtitle text-50">Last updated at {{ convertToDatetime($note->updated_at) }}</div>
        </div>
        @if(isAllAdmin() || isCoach())
            <x-buttons.dropdown :title="null" icon="more_vert" btnColor="outline-secondary" iconMargin="">
                <x-buttons.basic-button icon="edit" text="Edit Note" additionalClass="edit-note" :dropdownItem="true" :id="$note->id" color=""/>
                <x-buttons.basic-button icon="delete" text="Delete Note" additionalClass="delete-note" :dropdownItem="true" :id="$note->id" color="" iconColor="danger"/>
            </x-buttons.dropdown>
        @endif
    </div>
    <div class="card-body">
        <textarea class="form-control" rows="10">{{$note->note}}</textarea>
    </div>
</div>

@push('addon-script')
    <script>
        $(document).ready(function () {
            processWithConfirmation(
                '.delete-note',
                "{{ route('match-schedules.destroy-note', ['schedule' => $match->hash, 'note'=>':id']) }}",
                "{{ route('match-schedules.show', ['schedule' => $match->hash]) }}",
                'DELETE',
                "Are you sure to delete this note?",
                "Something went wrong when deleting this note!",
                "{{ csrf_token() }}"
            );
        });
    </script>
@endpush
