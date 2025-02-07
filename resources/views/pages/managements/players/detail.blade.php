@extends('layouts.master')
@section('title')
    {{ $data->user->firstName  }} {{ $data->user->lastName  }} Profile
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('modal')
    <x-modal.players-coaches.add-teams :route="route('player-managements.update-teams', ['player' => $data->hash])" :teams="$hasntJoinedTeams"/>
    <x-modal.change-password-modal :route="route('player-managements.change-password', ['player' => ':id'])"/>
    <x-modal.players-coaches.skill-assessments-modal/>
@endsection

@section('content')
    <nav class="navbar navbar-light border-bottom border-top">
        <div class="container">
            <ul class="nav navbar-nav">
                <li class="nav-item">
                    <a href="{{ route('player-managements.index') }}" class="nav-link text-70">
                        <i class="material-icons icon--left">keyboard_backspace</i>
                        Back to Player Lists
                    </a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="page-section bg-primary">
        <div class="container d-flex flex-column flex-md-row align-items-center text-center text-md-left">
            <img src="{{ Storage::url($data->user->foto) }}" width="104" height="104" class="mr-md-32pt mb-3 mb-md-0 rounded-circle img-object-fit-cover" alt="player-photo">
            <div class="flex mb-3 mb-md-0 ml-md-4">
                <h2 class="text-white mb-0">{{ getUserFullName($data->user)  }}</h2>
                <p class="lead text-white-50 d-flex align-items-center">Player - {{ $data->position->name }}</p>
            </div>
            @if(isAllAdmin())
                <x-buttons.dropdown title="Action" icon="keyboard_arrow_down" btnColor="outline-white" iconMargin="ml-3">
                    <x-buttons.link-button :dropdownItem="true" :href="route('player-managements.edit', $data->hash)"
                                           icon="edit" color="white" text="Edit player profile"/>
                    <x-buttons.basic-button icon="lock" color="white" text="Change player Account Password"
                                            additionalClass="changePassword" :dropdownItem="true" :id="$data->hash"/>
                    @if($data->user->status == '1')
                        <x-buttons.basic-button icon="check_circle" color="white" text="Deactivate player account"
                                                additionalClass="setDeactivate" :dropdownItem="true" :id="$data->hash"
                                                iconColor="danger"/>
                    @elseif($data->user->status == '0')
                        <x-buttons.basic-button icon="check_circle" color="white" text="Activate player account"
                                                additionalClass="setActivate" :dropdownItem="true" :id="$data->hash"
                                                iconColor="success"/>
                    @endif
                    <x-buttons.basic-button icon="delete" iconColor="danger" color="white" text="Delete player account"
                                            additionalClass="delete-user" :dropdownItem="true" :id="$data->hash"/>
                </x-buttons.dropdown>
            @endif
        </div>
    </div>

    <x-tabs.navbar>
        <x-tabs.item title="Overview" link="overview" :active="true"/>
        <x-tabs.item title="Profile" link="profile"/>
        <x-tabs.item title="Teams" link="teams"/>
        <x-tabs.item title="Skill Stats" link="skill-stats"/>
        <x-tabs.item title="Parents/Guardians" link="parents"/>
        <x-tabs.item title="Upcoming Matches" link="upcoming-match"/>
        <x-tabs.item title="Upcoming Training" link="upcoming-training"/>
        <x-tabs.item title="Training Histories" link="training-histories"/>
        <x-tabs.item title="Match Histories" link="match-histories"/>
        <x-tabs.item title="Player Performance Review" link="performance"/>
    </x-tabs.navbar>

    <div class="container page-section">
        <div class="tab-content">

            {{-- OVERVIEW --}}
            <div class="tab-pane fade show active" id="overview-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Overview</div>
                </div>
                <div class="row card-group-row">
                    @include('components.cards.stats-card', ['title' => 'Match Played','data' => $playerMatchPlayed, 'dataThisMonth' => $playerMatchPlayedThisMonth])
                    @include('components.cards.stats-card', ['title' => 'Minutes Played','data' => $playerStats['minutesPlayed'], 'dataThisMonth' => $playerStats['minutesPlayedThisMonth']])
                    @if($data->position == 'Goalkeeper (GK)')
                        @include('components.cards.stats-card', ['title' => 'Saves','data' => $playerStats['saves'], 'dataThisMonth' => $playerStats['savesThisMonth']])
                    @endif

                    @include('components.cards.stats-card', ['title' => 'shots','data' => $playerStats['shots'], 'dataThisMonth' => $playerStats['shotsThisMonth']])
                    @include('components.cards.stats-card', ['title' => 'passes','data' => $playerStats['passes'], 'dataThisMonth' => $playerStats['passesThisMonth']])

                    @include('components.cards.stats-card', ['title' => 'Fouls','data' => $playerStats['fouls'], 'dataThisMonth' => $playerStats['foulsThisMonth']])
                    @include('components.cards.stats-card', ['title' => 'yellowCards','data' => $playerStats['yellowCards'], 'dataThisMonth' => $playerStats['yellowCardsThisMonth']])
                    @include('components.cards.stats-card', ['title' => 'redCards','data' => $playerStats['redCards'], 'dataThisMonth' => $playerStats['redCardsThisMonth']])

                    @include('components.cards.stats-card', ['title' => 'Goals','data' => $playerStats['goals'], 'dataThisMonth' => $playerStats['goalsThisMonth']])
                    @include('components.cards.stats-card', ['title' => 'Assists','data' => $playerStats['assists'], 'dataThisMonth' => $playerStats['assistsThisMonth']])
                    @include('components.cards.stats-card', ['title' => 'Own Goals','data' => $playerStats['ownGoal'], 'dataThisMonth' => $playerStats['ownGoalThisMonth']])

                    @include('components.cards.stats-card', ['title' => 'Wins','data' => $matchResults['Win'], 'dataThisMonth' => $matchResults['WinThisMonth']])
                    @include('components.cards.stats-card', ['title' => 'Losses','data' => $matchResults['Lose'], 'dataThisMonth' => $matchResults['LoseThisMonth']])
                    @include('components.cards.stats-card', ['title' => 'Draws','data' => $matchResults['Draw'], 'dataThisMonth' => $matchResults['DrawThisMonth']])
                    @include('components.cards.stats-card', ['title' => 'WIn Rate (%)','data' => $winRate, 'dataThisMonth' => null])
                </div>
            </div>

            {{-- Profile / Contact Histories --}}
            <div class="tab-pane fade" id="profile-tab" role="tabpanel">
                <div class="row card-group-row">
                    <div class="col-sm-6 flex-column">
                        {{--Profile Section--}}
                        <div class="page-separator">
                            <div class="page-separator__text">Profile</div>
                        </div>
                        <div class="card card-sm card-group-row__card">
                            <div class="card-body flex-column">
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Account Status :</p></div>
                                    <span class="ml-auto p-2 badge badge-pill @if($data->user->status == '1') badge-success @else badge-danger @endif"> @if($data->user->status == '1') Active @else Non-Active @endif</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Player Skill :</p></div>
                                    <div class="ml-auto p-2 text-muted">{{ $data->skill }}</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Strong Foot :</p></div>
                                    <div class="ml-auto p-2 text-muted">{{ $data->strongFoot }}</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Height :</p></div>
                                    <div class="ml-auto p-2 text-muted">{{ $data->height }} CM</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Weight :</p></div>
                                    <div class="ml-auto p-2 text-muted">{{ $data->weight }} KG</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Date of Birth :</p></div>
                                    <div class="ml-auto p-2 text-muted">{{ $data->user->dob }}</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Age :</p></div>
                                    <div class="ml-auto p-2 text-muted">{{ getAge($data->user->dob) }}</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Gender :</p></div>
                                    <div class="ml-auto p-2 text-muted">{{ $data->user->gender }}</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Join Date :</p></div>
                                    <div class="ml-auto p-2 text-muted">{{ convertToDate($data->joinDate) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 flex-column">
                        {{--Contact Section--}}
                        <div class="page-separator">
                            <div class="page-separator__text">Contact</div>
                        </div>
                        <div class="card card-sm card-group-row__card">
                            <div class="card-body flex-column">
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Email :</p></div>
                                    <div class="ml-auto p-2 text-muted">{{ $data->user->email }}</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Phone Number :</p></div>
                                    <div class="ml-auto p-2 text-muted">{{ $data->user->phoneNumber }}</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Address :</p></div>
                                    <div class="ml-auto p-2 text-muted">{{ $data->user->address }}</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Country :</p></div>
                                    <div class="ml-auto p-2 text-muted">{{ $data->user->country->name }}</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">State :</p></div>
                                    <div class="ml-auto p-2 text-muted">{{ $data->user->state->name }}</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">City :</p></div>
                                    <div class="ml-auto p-2 text-muted">{{ $data->user->city->name }}</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Zip Code :</p></div>
                                    <div class="ml-auto p-2 text-muted">{{ $data->user->zipCode }}</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Created At :</p></div>
                                    <div class="ml-auto p-2 text-muted">{{ convertToDatetime($data->user->created_at) }}</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Last Updated :</p></div>
                                    <div class="ml-auto p-2 text-muted">{{ convertToDatetime($data->user->updated_at) }}</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Last Seen :</p></div>
                                    <div class="ml-auto p-2 text-muted">{{ convertToDatetime($data->user->lastSeen) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Teams Histories --}}
            <div class="tab-pane fade" id="teams-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Teams</div>
                    @if(isAllAdmin())
                        <x-buttons.basic-button icon="add" text="Add New Team" additionalClass="add-team" margin="ml-auto"/>
                    @endif
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover w-100" id="teamsTable">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Team Name</th>
                                    <th>Date Joined</th>
                                    @if(isAllAdmin())
                                        <th>Action</th>
                                    @endif
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Skill Stats Histories --}}
            <div class="tab-pane fade" id="skill-stats-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Latest Skill Stats</div>
                    @if(isCoach())
                        <x-buttons.basic-button icon="edit" text="Update player Skills" additionalClass="addSkills" margin="ml-auto"/>
                    @endif
                </div>
                <div class="card card-body">
                    <x-player-skill-stats-radar-chart :labels="$playerSkillStats['label']"
                                                      :datas="$playerSkillStats['data']" chartId="skillStatsChart"/>
                </div>
                {{--All Skill Stats Section--}}
                <x-cards.player-skill-stats-card :allSkills="$allSkills"/>

                {{--Skill Stats History Section--}}
                <x-chart.player-skill-history-chart :player="$data"/>
            </div>

            {{-- Parents/Guardians Histories --}}
            <div class="tab-pane fade" id="parents-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Parents/Guardians</div>
                    @if(isAllAdmin())
                        <x-buttons.link-button :href="route('player-managements.player-parents.create', $data->hash)" margin="ml-auto" icon="edit" color="primary" text="Add new parent/guardian"/>
                    @endif
                </div>
                <x-player-parents-tables :player="$data"/>
            </div>

            {{-- Upcoming Matches --}}
            <div class="tab-pane fade" id="upcoming-match-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Upcoming Matches</div>
                    <x-buttons.link-button :href="route('player-managements.upcoming-matches', $data->hash)" icon="chevron_right" color="white" text="View More" margin="ml-auto"/>
                </div>
                @if(count($playerUpcomingMatches) < 1)
                    <x-warning-alert text="There are no matches scheduled at this time"/>
                @endif
                @foreach($playerUpcomingMatches as $match)
                    <x-cards.match-card :match="$match" :latestMatch="false"/>
                @endforeach
            </div>

            {{-- Upcoming Trainings --}}
            <div class="tab-pane fade" id="upcoming-training-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Upcoming Trainings</div>
                    <x-buttons.link-button :href="route('player-managements.upcoming-trainings', $data->hash)" icon="chevron_right" color="white" text="View More" margin="ml-auto"/>
                </div>
                @if(count($playerUpcomingTrainings) < 1)
                    <x-warning-alert text="There are no trainings scheduled at this time"/>
                @endif
                <div class="row">
                    @foreach($playerUpcomingTrainings as $training)
                        <div class="col-lg-6">
                            <x-cards.training-card :training="$training"/>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Training Histories --}}
            <div class="tab-pane fade" id="training-histories-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Training Histories</div>
                </div>
                <x-tables.player-training-histories-table :player="$data"/>
            </div>

            {{-- Match Histories --}}
            <div class="tab-pane fade" id="match-histories-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Match Histories</div>
                </div>
                <x-tables.player-match-histories-table :player="$data"/>
            </div>

            {{-- player performance review --}}
            <div class="tab-pane fade" id="performance-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">player performance review</div>
                </div>
                <x-player-performance-review-table
                    :route="route('player-managements.performance-reviews.index-tables', ['player' => $data->hash])"
                    tableId="performanceReviewTable"/>
            </div>
        </div>
    </div>

@endsection
@push('addon-script')
    <script>
        $(document).ready(function () {
            $('#teamsTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! route('player-managements.player-teams', $data->hash) !!}',
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'name', name: 'name'},
                    {data: 'date', name: 'date'},
                    @if(isAllAdmin())
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                    @endif
                ],
            });

            @if(isAllAdmin())
            processWithConfirmation(
                ".setDeactivate",
                "{{ route('player-managements.deactivate', ':id') }}",
                "{{ route('player-managements.show', $data->hash) }}",
                "PATCH",
                "Are you sure to deactivate this player {{ getUserFullName($data->user) }}'s account?",
                "Something went wrong when deactivating this player {{ getUserFullName($data->user) }}'s account!",
                "{{ csrf_token() }}"
            );

            processWithConfirmation(
                ".setActivate",
                "{{ route('player-managements.activate', ':id') }}",
                "{{ route('player-managements.show', $data->hash) }}",
                "PATCH",
                "Are you sure to activate this player {{ getUserFullName($data->user) }}'s account?",
                "Something went wrong when activating this player {{ getUserFullName($data->user) }}'s account!",
                "{{ csrf_token() }}"
            );

            processWithConfirmation(
                ".delete-user",
                "{{ route('player-managements.destroy', ['player' => ':id']) }}",
                "{{ route('player-managements.index') }}",
                "DELETE",
                "Are you sure to delete this player {{ getUserFullName($data->user) }}'s account?",
                "Something went wrong when deleting this player {{ getUserFullName($data->user) }}'s account!",
                "{{ csrf_token() }}"
            );

            processWithConfirmation(
                ".delete-team",
                "{{ route('player-managements.remove-team', ['player' => $data->hash, 'team' => ':id']) }}",
                "{{ route('player-managements.show', $data->hash) }}",
                "DELETE",
                "Are you sure to remove player {{ getUserFullName($data->user) }} from this team?",
                "Something went wrong when removing player {{ getUserFullName($data->user) }} from this team!",
                "{{ csrf_token() }}"
            );
            @endif
        });
    </script>
@endpush
