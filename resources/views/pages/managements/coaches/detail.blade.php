@extends('layouts.master')
@section('title')
    Coach {{ getUserFullName($data->user)  }} Profile
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('modal')
    <x-change-password-modal :route="route('coach-managements.change-password', ['coach' => ':id'])"/>
    <x-modal.players-coaches.add-teams :route="route('coach-managements.update-team', ['coach' => $data->hash])" :teams="$teams"/>
@endsection

@section('content')
    <nav class="navbar navbar-light border-bottom border-top">
        <div class="container">
            <ul class="nav navbar-nav">
                <li class="nav-item">
                    <a href="{{ route('coach-managements.index') }}" class="nav-link text-70">
                        <i class="material-icons icon--left">keyboard_backspace</i> Back to Coach Lists
                    </a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="page-section bg-primary">
        <div class="container d-flex flex-column flex-md-row align-items-center text-center text-md-left">
            <img src="{{ Storage::url($data->user->foto) }}" width="104" height="104"
                 class="mr-md-32pt mb-3 mb-md-0 rounded-circle img-object-fit-cover" alt="instructor">
            <div class="flex mb-3 mb-md-0 ml-md-3">
                <h2 class="text-white mb-0">{{ getUserFullName($data->user)  }}</h2>
                <p class="lead text-white-50 d-flex align-items-center">{{ $data->specialization->name }}- {{ $data->certification->name }}</p>
            </div>
            <x-buttons.dropdown title="Action" icon="keyboard_arrow_down" btnColor="outline-white" iconMargin="ml-3">
                <x-buttons.link-button :dropdownItem="true" :href="route('coach-managements.edit', $data->hash)"
                                       icon="edit" color="white" text="Edit coach profile"/>
                <x-buttons.basic-button icon="lock" color="white" text="Change coach Account Password"
                                        additionalClass="changePassword" :dropdownItem="true" :id="$data->hash"/>
                @if($data->user->status == '1')
                    <x-buttons.basic-button icon="check_circle" color="white" text="Deactivate coach account"
                                            additionalClass="setDeactivate" :dropdownItem="true" :id="$data->hash"
                                            iconColor="danger"/>
                @elseif($data->user->status == '0')
                    <x-buttons.basic-button icon="check_circle" color="white" text="Activate coach account"
                                            additionalClass="setActivate" :dropdownItem="true" :id="$data->hash"
                                            iconColor="success"/>
                @endif
                <x-buttons.basic-button icon="delete" iconColor="danger" color="white" text="Delete coach account"
                                        additionalClass="delete-user" :dropdownItem="true" :id="$data->hash"/>
            </x-buttons.dropdown>
        </div>
    </div>

    <x-tabs.navbar>
        <x-tabs.item title="Overview" link="overview" :active="true"/>
        <x-tabs.item title="Profile" link="profile"/>
        <x-tabs.item title="Teams Managed" link="teams"/>
    </x-tabs.navbar>

    <div class="container page-section">
        <div class="tab-content">
            <div class="tab-pane fade show active" id="overview-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Overview</div>
                </div>
                <div class="row mb-4">
                    @include('components.cards.stats-card', ['title' => 'Matches','data' => $matchPlayed, 'dataThisMonth' => $matchPlayedThisMonth])
                    @include('components.cards.stats-card', ['title' => 'Wins','data' => $wins, 'dataThisMonth' => $winsThisMonth])
                    @include('components.cards.stats-card', ['title' => 'Losses','data' => $lose, 'dataThisMonth' => $loseThisMonth])
                    @include('components.cards.stats-card', ['title' => 'Draws','data' => $draw, 'dataThisMonth' => $drawThisMonth])
                    @include('components.cards.stats-card', ['title' => 'Win Rate (%)','data' => $winRate, 'dataThisMonth' => $winRateThisMonth])
                    @include('components.cards.stats-card', ['title' => 'Goals For','data' => $goals, 'dataThisMonth' => $goalsThisMonth])
                    @include('components.cards.stats-card', ['title' => 'Goal Against','data' => $goalConceded, 'dataThisMonth' => $goalConcededThisMonth])
                    @include('components.cards.stats-card', ['title' => 'Goal Differences','data' => $goalsDifference, 'dataThisMonth' => $goalsDifferenceThisMonth])
                    @include('components.cards.stats-card', ['title' => 'Clean Sheets','data' => $cleanSheets, 'dataThisMonth' => $cleanSheetsThisMonth])
                </div>
            </div>
            <div class="tab-pane fade" id="profile-tab" role="tabpanel">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="page-separator">
                            <div class="page-separator__text">Contact</div>
                        </div>
                        <div class="card">
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
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="page-separator">
                            <div class="page-separator__text">Profile</div>
                        </div>
                        <div class="card">
                            <div class="card-body flex-column">
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Status :</p></div>
                                    @if($data->user->status == '1')
                                        <span class="ml-auto p-2 badge badge-pill badge-success">Active</span>
                                    @else
                                        <span class="ml-auto p-2 badge badge-pill badge-danger">Non-Active</span>
                                    @endif
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Specialization :</p></div>
                                    <div class="ml-auto p-2 text-muted">{{ $data->specialization->name }}</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Certification Level :</p></div>
                                    <div class="ml-auto p-2 text-muted">{{ $data->certification->name }}</div>
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
                                    <div class="ml-auto p-2 text-muted">{{ convertToDate($data->user->dob) }}</div>
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
                                    <div class="p-2"><p class="card-title mb-4pt">Hired Date :</p></div>
                                    <div
                                        class="ml-auto p-2 text-muted">{{ convertToDate($data->hireDate) }}</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Created At :</p></div>
                                    <div
                                        class="ml-auto p-2 text-muted">{{ convertToDatetime($data->user->created_at) }}</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Last Updated :</p></div>
                                    <div
                                        class="ml-auto p-2 text-muted">{{ convertToDatetime($data->user->updated_at) }}</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Last Seen :</p></div>
                                    <div
                                        class="ml-auto p-2 text-muted">{{ convertToDatetime($data->user->lastSeen) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="teams-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Teams Managed</div>
                    <x-buttons.basic-button icon="add" text="Add New Team" additionalClass="add-team" margin="ml-auto"/>
                </div>
                <div class="card">
                    <div class="card-body">
                        <x-table :headers="['Team','Date Joined', 'Action']" tableId="teamsTable"/>
                    </div>
                </div>
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
                    url: '{!! route('coach-managements.coach-teams', $data->hash) !!}',
                },
                columns: [
                    {data: 'name', name: 'name'},
                    {data: 'date', name: 'date'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });

            processWithConfirmation(
                ".setDeactivate",
                "{{ route('coach-managements.deactivate', ':id') }}",
                "{{ route('coach-managements.show', $data->hash) }}",
                "PATCH",
                "Are you sure to deactivate this coach {{ getUserFullName($data->user) }}'s account?",
                "Something went wrong when deactivating this coach {{ getUserFullName($data->user) }}'s account!",
                "{{ csrf_token() }}"
            );

            processWithConfirmation(
                ".setActivate",
                "{{ route('coach-managements.activate', ':id') }}",
                "{{ route('coach-managements.show', $data->hash) }}",
                "PATCH",
                "Are you sure to activate this coach {{ getUserFullName($data->user) }}'s account?",
                "Something went wrong when activating this coach {{ getUserFullName($data->user) }}'s account!",
                "{{ csrf_token() }}"
            );

            processWithConfirmation(
                ".delete-user",
                "{{ route('coach-managements.destroy', ['coach' => ':id']) }}",
                "{{ route('coach-managements.index') }}",
                "DELETE",
                "Are you sure to delete this coach {{ getUserFullName($data->user) }}'s account?",
                "Something went wrong when deleting this coach {{ getUserFullName($data->user) }}'s account!",
                "{{ csrf_token() }}"
            );

            processWithConfirmation(
                ".delete-team",
                "{{ route('coach-managements.remove-team', ['coach' => $data->id, 'team' => ':id']) }}",
                "{{ route('coach-managements.show', $data->hash) }}",
                "DELETE",
                "Are you sure to remove coach {{ getUserFullName($data->user) }} from this team?",
                "Something went wrong when removing coach {{ getUserFullName($data->user) }} from this team!",
                "{{ csrf_token() }}"
            );
        });
    </script>
@endpush
