@extends('layouts.master')
@section('title')
    Coach {{ $fullName  }} Profile
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('modal')
    <x-change-password-modal :route="route('coach-managements.change-password', ['coach' => ':id'])"/>
    <x-add-teams-to-player-coach-modal :route="route('coach-managements.updateTeams', ['coach' => $data->id])" :teams="$teams"/>
@endsection

@section('content')
    <nav class="navbar navbar-light border-bottom border-top">
        <div class="container">
            <ul class="nav navbar-nav">
                <li class="nav-item">
                    <a href="{{ route('coach-managements.index') }}" class="nav-link text-70"><i
                            class="material-icons icon--left">keyboard_backspace</i> Back to Coach Lists</a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="page-section bg-primary">
        <div class="container d-flex flex-column flex-md-row align-items-center text-center text-md-left">
            <img src="{{ Storage::url($data->user->foto) }}"
                 width="104"
                 height="104"
                 class="mr-md-32pt mb-3 mb-md-0 rounded-circle img-object-fit-cover"
                 alt="instructor">
            <div class="flex mb-3 mb-md-0 ml-md-3">
                <h2 class="text-white mb-0">{{ $fullName  }}</h2>
                <p class="lead text-white-50 d-flex align-items-center">Coach
                    - {{ $data->specializations->name }} - {{ $data->certification->name }}</p>
            </div>
            <div class="dropdown">
                <button class="btn btn-outline-white" type="button" id="dropdownMenuButton" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                    Action
                    <span class="material-icons ml-3">
                        keyboard_arrow_down
                    </span>
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item" href="{{ route('coach-managements.edit', $data->id) }}"><span
                            class="material-icons">edit</span> Edit Coach Profile</a>
                    @if($data->user->status == '1')
                        <button type="submit" class="dropdown-item setDeactivate" id="{{$data->id}}">
                            <span class="material-icons text-danger">check_circle</span>
                            Deactivate Coach
                        </button>
                    @elseif($data->user->status == '0')
                        <button type="submit" class="dropdown-item setActivate" id="{{$data->id}}">
                            <span class="material-icons text-success">check_circle</span>
                            Activate Coach
                        </button>
                    @endif
                    <a class="dropdown-item changePassword" id="{{ $data->id }}"><span
                            class="material-icons">lock</span> Change Coach's Account Password</a>
                    <button type="button" class="dropdown-item delete-user" id="{{$data->id}}">
                        <span class="material-icons text-danger">delete</span> Delete Coach
                    </button>
                </div>
            </div>
        </div>
    </div>
    <nav class="navbar navbar-light border-bottom border-top py-3">
        <div class="container">
            <ul class="nav nav-pills">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#overview-tab">Overview</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#profile-tab">Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#teams-tab">Teams Managed</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container page-section">
        <div class="tab-content">
            <div class="tab-pane fade show active" id="overview-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Overview</div>
                </div>
                <div class="row mb-4">
                    @include('components.stats-card', ['title' => 'Match Played','data' => $dataOverview['totalMatchPlayed'], 'dataThisMonth' => $dataOverview['thisMonthTotalMatchPlayed']])
                    @include('components.stats-card', ['title' => 'Goals','data' => $dataOverview['totalGoals'], 'dataThisMonth' => $dataOverview['thisMonthTotalGoals']])
                    @include('components.stats-card', ['title' => 'Goal Conceded','data' => $dataOverview['totalGoalsConceded'], 'dataThisMonth' => $dataOverview['thisMonthTotalGoalsConceded']])
                    @include('components.stats-card', ['title' => 'Goal Differences','data' => $dataOverview['goalsDifference'], 'dataThisMonth' => $dataOverview['thisMonthGoalsDifference']])
                    @include('components.stats-card', ['title' => 'Clean Sheets','data' => $dataOverview['totalCleanSheets'], 'dataThisMonth' => $dataOverview['thisMonthTotalCleanSheets']])
                    @include('components.stats-card', ['title' => 'Own Goals','data' => $dataOverview['totalOwnGoals'], 'dataThisMonth' => $dataOverview['thisMonthTotalOwnGoals']])
                    @include('components.stats-card', ['title' => 'Wins','data' => $dataOverview['totalWins'], 'dataThisMonth' => $dataOverview['thisMonthTotalWins']])
                    @include('components.stats-card', ['title' => 'Losses','data' => $dataOverview['totalLosses'], 'dataThisMonth' => $dataOverview['thisMonthTotalLosses']])
                    @include('components.stats-card', ['title' => 'Draws','data' => $dataOverview['totalDraws'], 'dataThisMonth' => $dataOverview['thisMonthTotalDraws']])
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
                                        <span class="ml-auto p-2 badge badge-pill badge-success">Aktif</span>
                                    @elseif($data->user->status == '0')
                                        <span class="ml-auto p-2 badge badge-pill badge-danger">Non Aktif</span>
                                    @endif
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Specialization :</p></div>
                                    <div class="ml-auto p-2 text-muted">{{ $data->specializations->name }}</div>
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
                                    <div class="ml-auto p-2 text-muted">{{ date('M d, Y', strtotime($data->user->dob)) }}</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Age :</p></div>
                                    <div class="ml-auto p-2 text-muted">{{ $age }}</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Gender :</p></div>
                                    <div class="ml-auto p-2 text-muted">{{ $data->user->gender }}</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Hired Date :</p></div>
                                    <div
                                        class="ml-auto p-2 text-muted">{{ date('M d, Y', strtotime($data->hireDate)) }}</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Created At :</p></div>
                                    <div
                                        class="ml-auto p-2 text-muted">{{ date('l, M d, Y. h:i A', strtotime($data->user->created_at)) }}</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Last Updated :</p></div>
                                    <div
                                        class="ml-auto p-2 text-muted">{{ date('l, M d, Y. h:i A', strtotime($data->user->updated_at)) }}</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="p-2"><p class="card-title mb-4pt">Last Seen :</p></div>
                                    <div
                                        class="ml-auto p-2 text-muted">{{ date('l, M d, Y. h:i A', strtotime($data->user->lastSeen)) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="teams-tab" role="tabpanel">
                <div class="page-separator">
                    <div class="page-separator__text">Teams Managed</div>
                    <a href="#" class="btn btn-sm btn-primary ml-auto" id="add-team">
                        <span class="material-icons mr-2">
                            add
                        </span>
                        Add New
                    </a>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover w-100" id="teamsTable">
                                <thead>
                                <tr>
                                    <th>Team Name</th>
                                    <th>Date Joined</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(isAllAdmin())
        <x-process-data-confirmation btnClass=".setDeactivate"
                                     :processRoute="route('deactivate-coach', ':id')"
                                     :routeAfterProcess="route('coach-managements.show', $data->id)"
                                     method="PATCH"
                                     confirmationText="Are you sure to deactivate this coach {{ getUserFullName($data->user) }}'s account?"
                                     errorText="Something went wrong when deactivating this coach {{ getUserFullName($data->user) }}'s account!"/>

        <x-process-data-confirmation btnClass=".setActivate"
                                     :processRoute="route('activate-coach', ':id')"
                                     :routeAfterProcess="route('coach-managements.show', $data->id)"
                                     method="PATCH"
                                     confirmationText="Are you sure to activate this coach {{ getUserFullName($data->user) }}'s account?"
                                     errorText="Something went wrong when activating this coach {{ getUserFullName($data->user) }}'s account!"/>

        <x-process-data-confirmation btnClass=".delete-user"
                                     :processRoute="route('coach-managements.destroy', ['coach' => ':id'])"
                                     :routeAfterProcess="route('coach-managements.index')"
                                     method="DELETE"
                                     confirmationText="Are you sure to delete this coach {{ getUserFullName($data->user) }}'s account?"
                                     errorText="Something went wrong when deleting this coach {{ getUserFullName($data->user) }}'s account!"/>

        <x-process-data-confirmation btnClass=".delete-team"
                                     :processRoute="route('coach-managements.removeTeam', ['coach' => $data->id, 'team' => ':id'])"
                                     :routeAfterProcess="route('coach-managements.show', $data->id)"
                                     method="DELETE"
                                     confirmationText="Are you sure to to remove coach {{ getUserFullName($data->user) }} from this team?"
                                     errorText="Something went wrong when removing coach {{ getUserFullName($data->user) }} from this team!"/>
    @endif
@endsection

@push('addon-script')
    <script>
        $(document).ready(function () {
            $('#teamsTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! route('coach-managements.coach-teams', $data->id) !!}',
                },
                columns: [
                    {data: 'name', name: 'name'},
                    {data: 'date', name: 'date'},
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
        });
    </script>
@endpush
