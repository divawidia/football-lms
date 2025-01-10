<div class="card">
    <div class="card-header d-flex align-items-center">
        <div class="flex">
            <h4 class="card-title">{{ date('D, M d Y h:i A', strtotime($note->created_at)) }}</h4>
            <div class="card-subtitle text-50">Last updated at {{ date('D, M d Y h:i A', strtotime($note->updated_at)) }}</div>
        </div>
        @if(isAllAdmin() || isCoach())
            <x-buttons.dropdown title="" icon="more_vert">
                <x-buttons.basic-button icon="edit" text="Edit Note" additionalClass="edit-note" :dropdownItem="true" :id="$note->id" color=""/>
                <x-buttons.basic-button icon="delete" text="Delete Note" additionalClass="delete-note" :dropdownItem="true" :id="$note->id" color="" iconColor="danger"/>
            </x-buttons.dropdown>
        @endif
    </div>
    <div class="card-body">
        <textarea class="form-control" rows="10">{{$note->note}}</textarea>
    </div>
</div>

{{--    delete own goal player confirmation   --}}
@if($note->schedule->eventType == 'Training')
    <x-process-data-confirmation btnClass=".delete-note"
                              :processRoute="$deleteRoute"
                              :routeAfterProcess="route('training-schedules.show', ['schedule' => $note->schedule->hash])"
                              method="DELETE"
                              confirmationText="Are you sure to delete this note?"
                              errorText="Something went wrong when deleting the note!"/>
@else
    <x-process-data-confirmation btnClass=".delete-note"
                              :processRoute="$deleteRoute"
                              :routeAfterProcess="route('match-schedules.show', ['schedule' => $note->schedule->hash])"
                              method="DELETE"
                              confirmationText="Are you sure to delete this note?"
                              errorText="Something went wrong when deleting the note!"/>
@endif
