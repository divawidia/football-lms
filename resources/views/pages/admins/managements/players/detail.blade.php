@extends('layouts.master')
@section('title')
    {{ $fullName  }} Profile
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('content')
    <div class="page-section bg-primary">
        <div class="container page__container d-flex flex-column flex-md-row align-items-center text-center text-md-left">
            <img src="{{ \Illuminate\Support\Facades\Storage::url($user->foto) }}"
                 width="104"
                 height="104"
                 class="mr-md-32pt mb-32pt mb-md-0 rounded-circle img-object-fit-cover"
                 alt="instructor">
            <div class="flex mb-32pt mb-md-0">
                <h2 class="text-white mb-0">{{ $fullName  }}</h2>
                <p class="lead text-white-50 d-flex align-items-center">Player - {{ $user->player->position->name }} - {{ $team }}</p>
            </div>
            <a href="{{ route('player-managements.edit', $user->id) }}"
               class="btn btn-outline-white">Edit Profile</a>
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
                                <div class="card-title">Match Appearance</div>
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
                                <div class="card-title">Goals</div>
                                <p class="card-subtitle text-50">
                                    4
                                    <i class="material-icons text-success ml-4pt icon-16pt">keyboard_arrow_up</i>
                                    From Last Match
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
                                <div class="card-title">Assists</div>
                                <p class="card-subtitle text-50">
                                    4
                                    <i class="material-icons text-success ml-4pt icon-16pt">keyboard_arrow_up</i>
                                    From Last Match
                                </p>
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
                                <div class="card-title">Wins</div>
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
                                <div class="card-title">Losses</div>
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
                                <div class="card-title">Minutes Played</div>
                                <p class="card-subtitle text-50">
                                    4
                                    <i class="material-icons text-success ml-4pt icon-16pt">keyboard_arrow_up</i>
                                    From Last Match
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
                    <div class="page-separator__text">Profile</div>
                </div>
                <div class="card card-sm card-group-row__card">
                    <div class="card-body flex-column">
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Status :</p></div>
                            @if($user->status == '1')
                                <span class="ml-auto p-2 badge badge-pill badge-success">Aktif</span>
                            @elseif($user->status == '0')
                                <span class="ml-auto p-2 badge badge-pill badge-danger">Non Aktif</span>
                            @endif
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Player Skill :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ $user->player->skill }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Strong Foot :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ $user->player->strongFoot }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Height :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ $user->player->height }} CM</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Weight :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ $user->player->weight }} KG</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Date of Birth :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ date('M d, Y', strtotime($user->dob)) }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Age :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ $age }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Gender :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ $user->gender }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Join Date :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ date('M d, Y', strtotime($user->player->joinDate)) }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Created At :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ date('l, M d, Y. h:i A', strtotime($user->created_at)) }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Last Updated :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ date('l, M d, Y. h:i A', strtotime($user->updated_at)) }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Last Seen :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ date('l, M d, Y. h:i A', strtotime($user->lastSeen)) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 card-group-row__col flex-column">
                <div class="page-separator">
                    <div class="page-separator__text">Teams</div>
                    <a href="#" class="btn btn-primary ml-auto" id="add-parent" data-toggle="modal" data-target="#exampleModal">
                <span class="material-icons mr-2">
                    add
                </span>
                        Add New
                    </a>
                </div>
                <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="teamsTable">
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
                <div class="page-separator">
                    <div class="page-separator__text">Contact</div>
                </div>
                <div class="card card-sm card-group-row__card">
                    <div class="card-body flex-column">
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Email :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ $user->email }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Phone Number :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ $user->phoneNumber }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Address :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ $user->address }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Country :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ $user->country->name }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">State :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ $user->state->name }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">City :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ $user->city->name }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Zip Code :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ $user->zipCode }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="page-separator">
            <div class="page-separator__text">Parents/Guardians</div>
            <a href="#" class="btn btn-primary ml-auto" id="add-parent" data-toggle="modal" data-target="#exampleModal">
                <span class="material-icons mr-2">
                    add
                </span>
                Add New
            </a>
        </div>
        <div class="card dashboard-area-tabs p-relative o-hidden mb-lg-32pt">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="parentsTable">
                        <thead>
                        <tr>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Relation</th>
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

@endsection
@push('addon-script')
    <script>
        $(document).ready(function() {
            const parentsTable = $('#parentsTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! route('player-parents.index', $user->id) !!}',
                },
                columns: [
                    { data: 'firstName', name: 'firstName' },
                    { data: 'lastName', name: 'lastName' },
                    { data: 'email', name: 'email'},
                    { data: 'phoneNumber', name: 'phoneNumber' },
                    { data: 'relations', name: 'relations' },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '15%'
                    },
                ]
            });

            {{--$('body').on('click', '#add-parent', async function () {--}}
            {{--    const id = {{ $user->id }};--}}
            {{--    const {value: formValues} = await Swal.fire({--}}
            {{--        title: "Add New Parent/Guardian",--}}
            {{--        showCancelButton: true,--}}
            {{--        confirmButtonColor: "#3085d6",--}}
            {{--        cancelButtonColor: "#d33",--}}
            {{--        confirmButtonText: "Submit",--}}
            {{--        html: `--}}


            {{--                                            <div class="form-group">--}}
            {{--                                                <label class="form-label" for="firstName2">First name</label>--}}
            {{--                                                <input type="text"--}}
            {{--                                                       class="form-control @error('firstName') is-invalid @enderror"--}}
            {{--                                                       id="firstName"--}}
            {{--                                                       name="firstName"--}}
            {{--                                                       required--}}
            {{--                                                       value="{{ old('firstName') }}"--}}
            {{--                                                       placeholder="Input parent/guardian's first name ...">--}}
            {{--                                                @error('firstName')--}}
            {{--                                                <span class="invalid-feedback" role="alert">--}}
            {{--                                                    <strong>{{ $message }}</strong>--}}
            {{--                                                </span>--}}
            {{--                                                @enderror--}}
            {{--                                            </div>--}}
            {{--                                            <div class="form-group">--}}
            {{--                                                <label class="form-label" for="lastName2">Last name</label>--}}
            {{--                                                <input type="text"--}}
            {{--                                                       class="form-control @error('lastName') is-invalid @enderror"--}}
            {{--                                                       id="lastName"--}}
            {{--                                                       name="lastName"--}}
            {{--                                                       required--}}
            {{--                                                       value="{{ old('lastName') }}"--}}
            {{--                                                       placeholder="Input parent/guardian's last name ...">--}}
            {{--                                                @error('lastName')--}}
            {{--                                                <span class="invalid-feedback" role="alert">--}}
            {{--                                                    <strong>{{ $message }}</strong>--}}
            {{--                                                </span>--}}
            {{--                                                @enderror--}}
            {{--                                            </div>--}}
            {{--                                            <div class="form-group">--}}
            {{--                                                <label class="form-label" for="phoneNumber2">Phone Number</label>--}}
            {{--                                                <input type="text"--}}
            {{--                                                       class="form-control @error('phoneNumber') is-invalid @enderror"--}}
            {{--                                                       id="phoneNumber"--}}
            {{--                                                       name="phoneNumber"--}}
            {{--                                                       required--}}
            {{--                                                       value="{{ old('phoneNumber') }}"--}}
            {{--                                                       placeholder="Input parent/guardian's phone number ...">--}}
            {{--                                                @error('phoneNumber')--}}
            {{--                                                <span class="invalid-feedback" role="alert">--}}
            {{--                                                    <strong>{{ $message }}</strong>--}}
            {{--                                                </span>--}}
            {{--                                                @enderror--}}
            {{--                                            </div>--}}
            {{--                                            <div class="form-group">--}}
            {{--                                                <label class="form-label" for="email2">Email</label>--}}
            {{--                                                <input type="email"--}}
            {{--                                                       class="form-control @error('email') is-invalid @enderror"--}}
            {{--                                                       id="email"--}}
            {{--                                                       name="email"--}}
            {{--                                                       required--}}
            {{--                                                       value="{{ old('email') }}"--}}
            {{--                                                       placeholder="Input parent/guardian's email ...">--}}
            {{--                                                @error('email')--}}
            {{--                                                <span class="invalid-feedback" role="alert">--}}
            {{--                                                    <strong>{{ $message }}</strong>--}}
            {{--                                                </span>--}}
            {{--                                                @enderror--}}
            {{--                                            </div>--}}
            {{--                                            <div class="form-group">--}}
            {{--                                                <label class="form-label" for="relations">Relation to Player</label>--}}
            {{--                                                <select class="form-control form-select @error('relations') is-invalid @enderror" id="relations" name="relations" required>--}}
            {{--                                                    <option disabled selected>Select relation to player</option>--}}
            {{--                                                    @foreach(['Father', 'Mother', 'Brother', 'Sister', 'Others'] AS $relation)--}}
            {{--                                                        <option value="{{ $relation }}" @selected(old('relations') == $relation)>{{ $relation }}</option>--}}
            {{--                                                    @endforeach--}}
            {{--                                                </select>--}}
            {{--                                                @error('relations')--}}
            {{--                                                <span class="invalid-feedback" role="alert">--}}
            {{--                                                    <strong>{{ $message }}</strong>--}}
            {{--                                                </span>--}}
            {{--                                                @enderror--}}
            {{--                                            </div>--}}
            {{--          `,--}}
            {{--        focusConfirm: false,--}}
            {{--        preConfirm: () => {--}}
            {{--        //     try {--}}
            {{--        //         const githubUrl = `https://api.github.com/users/${login}`;--}}
            {{--        //         const response = await fetch(githubUrl);--}}
            {{--        //         if (!response.ok) {--}}
            {{--        //             return Swal.showValidationMessage(`--}}
            {{--        //               ${JSON.stringify(await response.json())}--}}
            {{--        //             `);--}}
            {{--        //         }--}}
            {{--        //         return response.json();--}}
            {{--        //     } catch (error) {--}}
            {{--        //         Swal.showValidationMessage(`Request failed: ${error}`);--}}
            {{--        //     }--}}
            {{--        // },--}}
            {{--        // allowOutsideClick: () => !Swal.isLoading()--}}
            {{--            return [--}}
            {{--                document.getElementById("firstName").value,--}}
            {{--                document.getElementById("lastName").value,--}}
            {{--                document.getElementById("phoneNumber").value,--}}
            {{--                document.getElementById("email").value,--}}
            {{--                document.getElementById("relations").value--}}
            {{--            ];--}}
            {{--        }--}}
            {{--    });--}}
            {{--    if (formValues) {--}}
            {{--        $.ajax({--}}
            {{--            url: "{{ route('player-parents.store', ['player' => ':id']) }}".replace(':id', id),--}}
            {{--            type: 'POST',--}}
            {{--            data: {--}}
            {{--                firstName: document.getElementById("firstName").value,--}}
            {{--                lastName: document.getElementById("lastName").value,--}}
            {{--                phoneNumber: document.getElementById("phoneNumber").value,--}}
            {{--                email: document.getElementById("email").value,--}}
            {{--                relations: document.getElementById("relations").value,--}}
            {{--                _token: "{{ csrf_token() }}"--}}
            {{--            },--}}
            {{--            success: function(response) {--}}
            {{--                Swal.fire({--}}
            {{--                    icon: "success",--}}
            {{--                    title: "Player's account successfully deleted!",--}}
            {{--                });--}}
            {{--                parentsTable.ajax.reload();--}}
            {{--            },--}}
            {{--            error: function(error) {--}}
            {{--                Swal.fire({--}}
            {{--                    icon: "error",--}}
            {{--                    title: "Oops...",--}}
            {{--                    text: error,--}}
            {{--                });--}}
            {{--            }--}}
            {{--        });--}}
            {{--    }--}}
            {{--});--}}
        });
    </script>
@endpush
