<div class="row">
    @foreach($coaches as $coach)
        <div class="col-md-6">
            <div class="card @if($coach->pivot->attendanceStatus == 'Required Action') border-warning @elseif($coach->pivot->attendanceStatus == 'Attended') border-success @else border-danger @endif" id="{{$coach->id}}">
                <div class="card-body d-flex align-items-center flex-row text-left">
                    <img src="{{ Storage::url($coach->user->foto) }}"
                         width="50"
                         height="50"
                         class="rounded-circle img-object-fit-cover"
                         alt="instructor">
                    <div class="flex ml-3">
                        <h5 class="mb-0">{{ $coach->user->firstName }} {{ $coach->user->lastName }}</h5>
                        <p class="text-50 lh-1 mb-0">{{ $coach->specialization->name }}</p>
                    </div>
                    <a class="btn @if($coach->pivot->attendanceStatus == 'Required Action') btn-outline-warning text-warning @elseif($coach->pivot->attendanceStatus == 'Attended') btn-outline-success text-success @else btn-outline-danger text-danger @endif coachAttendance" id="{{$coach->id}}" href="#">
                        <span class="material-icons mr-2">
                            @if($coach->pivot->attendanceStatus == 'Required Action') error
                            @elseif($coach->pivot->attendanceStatus == 'Attended') check_circle
                            @else cancel
                            @endif
                        </span>
                        {{ $coach->pivot->attendanceStatus }}
                    </a>
                </div>
            </div>
        </div>
    @endforeach
</div>
