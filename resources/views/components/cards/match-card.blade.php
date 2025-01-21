<a class="card" href="{{ route('match-schedules.show', $match->hash) }}">
    <div class="card-body">
        <div class="row">
            <div class="col-4 d-flex flex-column flex-md-row align-items-center">
                <img src="{{ Storage::url($match->homeTeam->logo) }}"
                     width="50"
                     height="50"
                     class="rounded-circle img-object-fit-cover"
                     alt="team-logo">
                <div class="ml-md-3 text-center text-md-left">
                    <h6 class="mb-0">{{ $match->homeTeam->teamName }}</h6>
                    <p class="text-50 lh-1 mb-0">{{ $match->homeTeam->ageGroup }}</p>
                </div>
            </div>
            <div class="col-4 text-center">
                <h2 class="mb-0">
                    @if($latestMatch == true)
                        {{ $match->homeTeam->pivot->teamScore }}
                        -
                        @if($match->matchType == 'Internal Match')
                            {{ $match->awayTeam->pivot->teamScore }}
                        @else
                            {{ $match->externalTeam->teamScore }}
                        @endif
                    @else
                        Vs.
                    @endif
                </h2>
            </div>
            <div class="col-4 d-flex flex-column-reverse flex-md-row align-items-center justify-content-end">
                @if($match->matchType == 'Internal Match')
                    <div class="mr-md-3 text-center text-md-right">
                        <h5 class="mb-0">{{ $match->awayTeam->teamName }}</h5>
                        <p class="text-50 lh-1 mb-0">{{$match->awayTeam->ageGroup}}</p>
                    </div>
                    <img src="{{ Storage::url($match->awayTeam->logo) }}"
                         width="50"
                         height="50"
                         class="rounded-circle img-object-fit-cover"
                         alt="team-logo">
                @else
                    <div class="mr-md-3 text-center text-md-right">
                        <h5 class="mb-0">{{ $match->externalTeam->teamName }}</h5>
                    </div>
                @endif
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
