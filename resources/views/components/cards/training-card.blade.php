<a class="card" href="{{ route('training-schedules.show', $training->id) }}">
    <div class="card-body">
        <div class="row">
            <div class="col-6 d-flex flex-column flex-md-row align-items-center">
                <img src="{{ Storage::url($training->teams[0]->logo) }}"
                     width="50"
                     height="50"
                     class="rounded-circle img-object-fit-cover"
                     alt="team-logo">
                <div class="ml-md-3 text-center text-md-left">
                    <h5 class="mb-0">{{$training->teams[0]->teamName}}</h5>
                    <p class="text-50 lh-1 mb-0">{{$training->teams[0]->ageGroup}}</p>
                </div>
            </div>
            <div class="col-6 d-flex flex-column">
                <div class="mr-2">
                    <i class="material-icons text-danger icon--left icon-16pt">event</i>
                    {{ date('D, M d Y', strtotime($training->date)) }}
                </div>
                <div class="mr-2">
                    <i class="material-icons text-danger icon--left icon-16pt">schedule</i>
                    {{ date('h:i A', strtotime($training->startTime)) }}
                    - {{ date('h:i A', strtotime($training->endTime)) }}
                </div>
                <div>
                    <i class="material-icons text-danger icon--left icon-16pt">location_on</i>
                    {{ $training->place }}
                </div>
            </div>
        </div>
    </div>
</a>
