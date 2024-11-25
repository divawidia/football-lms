<div class="card">
    <div class="card-header d-flex align-items-center">
        <div class="flex">
            <h4 class="card-title">{{ date('D, M d Y h:i A', strtotime($note->created_at)) }}</h4>
            <div class="card-subtitle text-50">Last updated at {{ date('D, M d Y h:i A', strtotime($note->updated_at)) }}</div>
        </div>
        @if(isAllAdmin() || isCoach())
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="material-icons">more_vert</span>
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item edit-note" id="{{ $note->id }}" href="">
                        <span class="material-icons">edit</span>Edit Note
                    </a>
                    <button type="button" class="dropdown-item delete-note" id="{{ $note->id }}">
                        <span class="material-icons text-danger">delete</span>Delete Note
                    </button>
                </div>
            </div>
        @endif
    </div>
    <div class="card-body">
        @php
            echo $note->note
        @endphp
    </div>
</div>

{{--    delete own goal player confirmation   --}}
<x-process-data-confirmation btnClass=".delete-note"
                             :processRoute="$deleteRoute"
                             :routeAfterProcess="route('match-schedules.show', ['schedule' => $note->scheduleId])"
                             method="DELETE"
                             confirmationText="Are you sure to delete this note?"
                             errorText="Something went wrong when deleting the note!"/>
