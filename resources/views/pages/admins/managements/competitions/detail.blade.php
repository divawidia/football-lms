@extends('layouts.master')
@section('title')
    Competition {{ $competition->teamName  }}
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('modal')
    <!-- Modal edit group modal -->
    <div class="modal fade" id="editGroupModal" tabindex="-1" aria-labelledby="editGroupModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="#" method="post" id="formEditGroupModal">
                    @method('PUT')
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Edit Group Division</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="groupId">
                        <div class="form-group ">
                            <label class="form-label" for="add_groupName">Group Division Name</label>
                            <small class="text-danger">*</small>
                            <input type="text"
                                   id="add_groupName"
                                   name="groupName"
                                   value="{{ old('groupName') }}"
                                   class="form-control"
                                   placeholder="Input group's name ...">
                            <span class="invalid-feedback groupName_error" role="alert">
                                <strong></strong>
                            </span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="page-section bg-primary">
        <div class="container page__container d-flex flex-column flex-md-row align-items-center text-center text-md-left">
            <img src="{{ Storage::url($competition->logo) }}"
                 width="104"
                 height="104"
                 class="mr-md-32pt mb-32pt mb-md-0 rounded-circle img-object-fit-cover"
                 alt="instructor">
            <div class="flex mb-32pt mb-md-0">
                <h2 class="text-white mb-0">{{ $competition->name  }}</h2>
                <p class="lead text-white-50 d-flex align-items-center">{{ $competition->type }}</p>
            </div>
            <div class="dropdown">
                <button class="btn btn-outline-white" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Action
                    <span class="material-icons ml-3">
                        keyboard_arrow_down
                    </span>
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item" href="{{ route('competition-managements.edit', $competition->id) }}"><span class="material-icons">edit</span> Edit Competition Info</a>
                    @if($competition->status == '1')
                        <form action="{{ route('deactivate-competition', $competition->id) }}" method="POST">
                            @method("PATCH")
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <span class="material-icons">block</span> Deactivate Competition
                            </button>
                        </form>
                    @else
                        <form action="{{ route('activate-competition', $competition->id) }}" method="POST">
                            @method("PATCH")
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <span class="material-icons">check_circle</span> Activate Competition
                            </button>
                        </form>
                    @endif
                    <button type="button" class="dropdown-item delete" id="{{$competition->id}}">
                        <span class="material-icons">delete</span> Delete Competition
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container page__container page-section">
        <div class="page-separator">
            <div class="page-separator__text">Overview</div>
        </div>
        <div class="row card-group-row mb-4">
            <div class="col-lg-4 card-group-row__col flex-column">
                <div class="card border-1 border-left-3 border-left-accent mb-lg-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">12</div>
                            <div class="ml-auto text-right">
                                <div class="card-title">Total Teams</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 card-group-row__col flex-column">
                <div class="card border-1 border-left-3 border-left-accent mb-lg-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">12</div>
                            <div class="ml-auto text-right">
                                <div class="card-title">Total Match</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 card-group-row__col flex-column">
                <div class="card border-1 border-left-3 border-left-accent mb-lg-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">2</div>
                            <div class="ml-auto text-right">
                                <div class="card-title text-capitalize">Toal Groups</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row card-group-row mb-4">
            <div class="col-lg-4 card-group-row__col flex-column">
                <div class="card border-1 border-left-3 border-left-accent mb-lg-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">12</div>
                            <div class="ml-auto text-right">
                                <div class="card-title text-capitalize">wins</div>
                                <p class="card-subtitle text-50">
                                    4
                                    <i class="material-icons text-success ml-4pt icon-16pt">keyboard_arrow_up</i>
                                    From Last Month
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 card-group-row__col flex-column">
                <div class="card border-1 border-left-3 border-left-accent mb-lg-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">12</div>
                            <div class="ml-auto text-right">
                                <div class="card-title text-capitalize">losses</div>
                                <p class="card-subtitle text-50">
                                    4
                                    <i class="material-icons text-success ml-4pt icon-16pt">keyboard_arrow_up</i>
                                    From Last Month
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 card-group-row__col flex-column">
                <div class="card border-1 border-left-3 border-left-accent mb-lg-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex d-flex align-items-center">
                            <div class="h2 mb-0 mr-3">12</div>
                            <div class="ml-auto text-right">
                                <div class="card-title text-capitalize">draws</div>
                                <p class="card-subtitle text-50">
                                    4
                                    <i class="material-icons text-success ml-4pt icon-16pt">keyboard_arrow_up</i>
                                    From Last Month
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row card-group-row">
            <div class="col-sm-6 card-group-row__col flex-column">
                <div class="page-separator">
                    <div class="page-separator__text">Competition Info</div>
                </div>
                <div class="card card-sm card-group-row__card">
                    <div class="card-body flex-column">
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Status :</p></div>
                            <div class="ml-auto p-2 text-muted">
                                @if ($competition->status == '1')
                                    <span class="badge badge-pill badge-success">Aktif</span>
                                @elseif($competition->status == '0')
                                    <span class="badge badge-pill badge-danger">Non Aktif</span>
                                @endif
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Start Date :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ $competition->startDate }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">End Date :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ $competition->endDate }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Location :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ $competition->location }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Contact Name :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ $competition->contactName }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Contact Phone :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ $competition->contactPhone }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Description :</p></div>
                            <div class="ml-auto p-2 text-muted">@php echo $competition->description @endphp</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Created At :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ date('l, M d, Y. h:i A', strtotime($competition->created_at)) }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Last Updated :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ date('l, M d, Y. h:i A', strtotime($competition->updated_at)) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 card-group-row__col flex-column">
                <div class="page-separator">
                    <div class="page-separator__text">Match</div>
                </div>
                <div class="card card-sm card-group-row__card">
                    <div class="card-body flex-column">

                    </div>
                </div>
            </div>
        </div>
        <div class="page-separator">
            <div class="page-separator__text">Group Divisions</div>
            <a href="{{ route('division-managements.create', $competition->id) }}" class="btn btn-primary ml-auto btn-sm">
                <span class="material-icons mr-2">
                    add
                </span>
                Add New
            </a>
        </div>
        <div class="row">
            @foreach($competition->groups as $group)
                @if(count($competition->groups) <= 1)
                    <div class="col-12">
                @else
                    <div class="col-lg-6">
                @endif
                    <div class="page-separator">
                        <div class="page-separator__text">{{ $group->groupName }}</div>
                        <div class="btn-toolbar ml-auto" role="toolbar" aria-label="Toolbar with button groups">
                                <a class="btn btn-sm btn-white edit-group" id="{{ $group->id }}" href="#" data-toggle="tooltip" data-placement="bottom" title="Edit Group">
                                    <span class="material-icons">edit</span>
                                </a>
                                <a href="{{ route('division-managements.addTeam', ['competition' => $competition->id, 'group' => $group->id]) }}" class="btn btn-sm btn-white ml-1" data-toggle="tooltip" data-placement="bottom" title="Add Team">
                                    <span class="material-icons">add</span>
                                </a>
                                <button type="button" class="btn btn-sm btn-white ml-1 delete-group" id="{{ $group->id }}" data-toggle="tooltip" data-placement="bottom" title="Delete Group">
                                    <span class="material-icons">delete</span>
                                </button>
                        </div>
                    </div>
                    <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" id="groupTable{{$group->id}}">
                                    <thead>
                                    <tr>
                                        <th>Team Name</th>
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
            @endforeach
        </div>
        <div class="page-separator">
            <div class="page-separator__text">Group Tables</div>
        </div>
        @foreach($competition->groups as $group)
            <div class="page-separator">
                <div class="page-separator__text">{{ $group->groupName }}</div>
                <div class="btn-toolbar ml-auto" role="toolbar" aria-label="Toolbar with button groups">
                    <a class="btn btn-sm btn-white edit-group" id="{{ $group->id }}" href="#" data-toggle="tooltip" data-placement="bottom" title="Edit Group">
                        <span class="material-icons">edit</span>
                    </a>
                    <a href="{{ route('division-managements.addTeam', ['competition' => $competition->id, 'group' => $group->id]) }}" class="btn btn-sm btn-white ml-1" data-toggle="tooltip" data-placement="bottom" title="Add Team">
                        <span class="material-icons">add</span>
                    </a>
                    <button type="button" class="btn btn-sm btn-white ml-1 delete-group" id="{{ $group->id }}" data-toggle="tooltip" data-placement="bottom" title="Delete Group">
                        <span class="material-icons">delete</span>
                    </button>
                </div>
            </div>
            <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="classTable{{$group->id}}">
                            <thead>
                            <tr>
                                <th>Team</th>
                                <th>Match Played</th>
                                <th>won</th>
                                <th>drawn</th>
                                <th>lost</th>
                                <th>goals For</th>
                                <th>goals Againts</th>
                                <th>goals Difference</th>
                                <th>red Cards</th>
                                <th>yellow Cards</th>
                                <th>points</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

@endsection
@push('addon-script')
    <script>
        $(document).ready(function() {
            @foreach($competition->groups as $group)
                const groupTable{{$group->id}} = $('#groupTable{{$group->id}}').DataTable({
                    processing: true,
                    serverSide: true,
                    ordering: true,
                    ajax: {
                        url: '{!! route('division-managements.index', ['competition'=>$competition->id,'group'=>$group->id]) !!}',
                    },
                    columns: [
                        { data: 'teams', name: 'teams' },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            width: '15%'
                        },
                    ]
                });

            const classTable{{$group->id}} = $('#classTable{{$group->id}}').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! route('division-managements.index', ['competition'=>$competition->id,'group'=>$group->id]) !!}',
                },
                columns: [
                    { data: 'teams', name: 'teams' },
                    { data: 'pivot.matchPlayed', name: 'pivot.matchPlayed' },
                    { data: 'pivot.won', name: 'pivot.won' },
                    { data: 'pivot.drawn', name: 'pivot.drawn' },
                    { data: 'pivot.lost', name: 'pivot.lost' },
                    { data: 'pivot.goalsFor', name: 'pivot.goalsFor' },
                    { data: 'pivot.goalsAgaints', name: 'pivot.goalsAgaints' },
                    { data: 'pivot.goalsDifference', name: 'pivot.goalsDifference' },
                    { data: 'pivot.redCards', name: 'pivot.redCards' },
                    { data: 'pivot.yellowCards', name: 'pivot.yellowCards' },
                    { data: 'pivot.points', name: 'pivot.points' },
                ]
            });
            @endforeach

            // show modal edit group data
            $('body').on('click', '.edit-group', function(e) {
                const id = $(this).attr('id');
                e.preventDefault();

                $.ajax({
                    method: 'GET',
                    url: "{{ route('division-managements.edit', ['competition' => $competition->id, 'group' => ':id']) }}".replace(':id', id),
                    success: function(res) {
                        $("#editGroupModal").modal('show');
                        $('#groupId').val(id);
                        $('#add_groupName').val(res.groupName);
                    },
                    error: function(xhr) {
                        const response = JSON.parse(xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            html: response,
                            allowOutsideClick: true,
                        });
                    }
                });
            });

            // insert data opponent team
            $('#formEditGroupModal').on('submit', function(e) {
                e.preventDefault();
                let id = $('#groupId').val();

                $.ajax({
                    method: $(this).attr('method'),
                    url: "{{ route('division-managements.update', ['competition' => $competition->id, 'group' => ':id']) }}".replace(':id', id),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    success: function(res) {
                        $('#editGroupModal').modal('hide');
                        Swal.fire({
                            title: 'Group Division successfully added!',
                            icon: 'success',
                            showCancelButton: false,
                            confirmButtonColor: "#1ac2a1",
                            confirmButtonText:
                                'Ok!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    },
                    error: function(xhr) {
                        const response = JSON.parse(xhr.responseText);
                        $.each(response.errors, function(key, val) {
                            $('span.' + key + '_error').text(val[0]);
                            $("input#add_" + key).addClass('is-invalid');
                        });
                    }
                });
            });

            $('.delete-group').on('click', function() {
                let id = $(this).attr('id');

                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#1ac2a1",
                    cancelButtonColor: "#E52534",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('division-managements.destroy', ['competition' => $competition->id,'group' => ':id']) }}".replace(':id', id),
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function() {
                                Swal.fire({
                                    icon: "success",
                                    title: "Group division successfully deleted!",
                                    showCancelButton: false,
                                    confirmButtonColor: "#1ac2a1",
                                    confirmButtonText:
                                        'Ok!'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        location.reload();
                                    }
                                });
                            },
                            error: function(error) {
                                Swal.fire({
                                    icon: "error",
                                    title: "Something went wrong",
                                    text: error,
                                });
                            }
                        });
                    }
                });
            });

            // delete competition alert
            $('body').on('click', '.delete', function() {
                let id = $(this).attr('id');

                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#1ac2a1",
                    cancelButtonColor: "#E52534",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('competition-managements.destroy', ['competition' => ':id']) }}".replace(':id', id),
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function() {
                                Swal.fire({
                                    title: 'Competition successfully deleted!',
                                    icon: 'success',
                                    showCancelButton: false,
                                    confirmButtonColor: "#1ac2a1",
                                    confirmButtonText:
                                        'Ok!'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = "{{ route('competition-managements.index') }}";
                                    }
                                });
                            },
                            error: function(error) {
                                Swal.fire({
                                    icon: "error",
                                    title: "Something went wrong when deleting data!",
                                    text: error,
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
