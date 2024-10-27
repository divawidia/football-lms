<div class="row">
    @foreach($players as $player)
        <div class="col-md-6">
            <div class="card @if($player->pivot->attendanceStatus == 'Required Action') border-warning @elseif($player->pivot->attendanceStatus == 'Attended') border-success @else border-danger @endif" id="{{$player->id}}">
                <div class="card-body d-flex align-items-center flex-row text-left">
                    <img src="{{ Storage::url($player->user->foto) }}"
                         width="50"
                         height="50"
                         class="rounded-circle img-object-fit-cover"
                         alt="instructor">
                    <div class="flex ml-3">
                        <h5 class="mb-0">{{ $player->user->firstName  }} {{ $player->user->lastName  }}</h5>
                        <p class="text-50 lh-1 mb-0">{{ $player->position->name }}</p>
                    </div>
                    <a class="btn @if($player->pivot->attendanceStatus == 'Required Action') btn-outline-warning text-warning @elseif($player->pivot->attendanceStatus == 'Attended') btn-outline-success text-success @else btn-outline-danger text-danger @endif playerAttendance" id="{{$player->id}}" href="#">
                        <span class="material-icons mr-2">
                            @if($player->pivot->attendanceStatus == 'Required Action') error
                            @elseif($player->pivot->attendanceStatus == 'Attended') check_circle
                            @else cancel
                            @endif
                        </span>
                        {{ $player->pivot->attendanceStatus }}
                    </a>
                </div>
            </div>
        </div>
    @endforeach
</div>

{!! $players->links('pagination::bootstrap-5') !!}
