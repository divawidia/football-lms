<a class="card" href="{{ route('match-schedules.show', $match->id) }}">
    <div class="card-body">
        <div class="row">
            <div class="col-4 d-flex flex-column flex-md-row align-items-center">
                <img src="{{ Storage::url($match->teams[0]->logo) }}"
                     width="50"
                     height="50"
                     class="rounded-circle img-object-fit-cover"
                     alt="team-logo">
                <div class="ml-md-3 text-center text-md-left">
                    <h6 class="mb-0">{{ $match->teams[0]->teamName }}</h6>
                    <p class="text-50 lh-1 mb-0">{{ $match->teams[0]->ageGroup }}</p>
                </div>
            </div>
            <div class="col-4 text-center">
                <h2 class="mb-0">
                    @if($latestMatch == true)
                        {{ $match->teams[0]->pivot->teamScore }} - {{ $match->teams[1]->pivot->teamScore }}
                    @else
                        Vs.
                    @endif
                </h2>
            </div>
            <div
                class="col-4 d-flex flex-column-reverse flex-md-row align-items-center justify-content-end">
                <div class="mr-md-3 text-center text-md-right">
                    <h6 class="mb-0">{{ $match->teams[1]->teamName }}</h6>
                    <p class="text-50 lh-1 mb-0">{{ $match->teams[1]->ageGroup }}</p>
                </div>
                <img src="{{ Storage::url($match->teams[1]->logo) }}"
                     width="50"
                     height="50"
                     class="rounded-circle img-object-fit-cover"
                     alt="team-logo">
            </div>
        </div>

        <div class="row justify-content-center mt-3">
            <div class="mr-2">
                <i class="material-icons text-danger icon--left icon-16pt">event</i>
                {{ date('D, M d Y', strtotime($match->date)) }}
            </div>
            <div class="mr-2">
                <i class="material-icons text-danger icon--left icon-16pt">schedule</i>
                {{ date('h:i A', strtotime($match->startTime)) }}
                - {{ date('h:i A', strtotime($match->endTime)) }}
            </div>
            <div>
                <i class="material-icons text-danger icon--left icon-16pt">location_on</i>
                {{ $match->place }}
            </div>
        </div>
    </div>
</a>
