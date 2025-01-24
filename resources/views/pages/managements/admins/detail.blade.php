@extends('layouts.master')
@section('title')
    {{ $fullName  }} Profile
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('modal')
    <x-change-password-modal :route="route('admin-managements.change-password', ['admin' => ':id'])"/>
@endsection

@section('content')
    <nav class="navbar navbar-light border-bottom border-top px-0">
        <div class="container page__container">
            <ul class="nav navbar-nav">
                <li class="nav-item">
                    <a href="{{ route('admin-managements.index') }}" class="nav-link text-70"><i
                            class="material-icons icon--left">keyboard_backspace</i> Back to Admin Lists</a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="page-section bg-primary">
        <div
            class="container page__container d-flex flex-column flex-md-row align-items-center text-center text-md-left">
            <img src="{{ Storage::url($data->user->foto) }}"
                 width="104"
                 height="104"
                 class="mr-md-32pt mb-3 mb-md-0 rounded-circle img-object-fit-cover"
                 alt="player-photo">
            <div class="flex mb-3 mb-md-0 ml-md-4">
                <h2 class="text-white mb-0">{{ $fullName  }}</h2>
                <p class="lead text-white-50 d-flex align-items-center">{{ $data->position }}</p>
            </div>
            @if(isSuperAdmin() && getLoggedUser()->id != $data->user->id)
                <x-buttons.dropdown title="Action" icon="keyboard_arrow_down" btnColor="outline-white" iconMargin="ml-3">
                    <x-buttons.link-button :dropdownItem="true" :href="route('admin-managements.edit', $data->hash)" icon="edit" color="white" text="Edit admin profile"/>
                    @if($data->user->status == '1')
                        <x-buttons.basic-button icon="check_circle" color="white" text="Deactivate Admin" additionalClass="setDeactivate" :dropdownItem="true" :id="$data->hash" iconColor="danger"/>
                    @elseif($data->user->status == '0')
                        <x-buttons.basic-button icon="check_circle" color="white" text="Activate Admin" additionalClass="setActivate" :dropdownItem="true" :id="$data->hash" iconColor="success"/>
                    @endif
                    <x-buttons.basic-button icon="lock" color="white" text="Change admins Account Password" additionalClass="changePassword" :dropdownItem="true" :id="$data->hash"/>
                    <x-buttons.basic-button icon="delete" iconColor="danger" color="white" text="Delete admin" additionalClass="deleteAdmin" :dropdownItem="true" :id="$data->hash"/>
                </x-buttons.dropdown>
            @endif
        </div>
    </div>

    <div class="container page__container page-section">
        <div class="page-separator">
            <div class="page-separator__text">Account Profile</div>
        </div>

        <div class="row card-group-row mb-8pt">

            <div class="col-sm-6 card-group-row__col">
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
                            <div class="p-2"><p class="card-title mb-4pt">Position :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ $data->position }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Last Seen :</p></div>
                            <div
                                class="ml-auto p-2 text-muted">{{ date('l, M d, Y. h:i A', strtotime($data->user->lastSeen)) }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Date of Birth :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ date('M d, Y', strtotime($data->user->dob)) }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Gender :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ $data->user->gender }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Hired Date :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ date('M d, Y', strtotime($data->hireDate)) }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Created At :</p></div>
                            <div
                                class="ml-auto p-2 text-muted">{{ date('l, M d, Y. h:i A', strtotime($data->user->created_at)) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 card-group-row__col">
                <div class="card card-sm card-group-row__card">
                    <div class="card-body flex-column">
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Status :</p></div>
                            @if($data->user->status == '1')
                                <span class="ml-auto p-2 badge badge-pill badge-success">Active</span>
                            @elseif($data->user->status == '0')
                                <span class="ml-auto p-2 badge badge-pill badge-danger">Non-Active</span>
                            @endif
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
                            <div class="p-2"><p class="card-title mb-4pt">Last Updated :</p></div>
                            <div
                                class="ml-auto p-2 text-muted">{{ date('l, M d, Y. h:i A', strtotime($data->user->updated_at)) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('addon-script')
    <script>
        $(document).ready(function () {
            processWithConfirmation(
                '.setDeactivate',
                "{{ route('admin-managements.deactivate', ':id') }}",
                "{{ route('admin-managements.show', $data->id) }}",
                'PATCH',
                "Are you sure to deactivate this admin account's status?",
                "Something went wrong when deactivating this admin account!",
                "{{ csrf_token() }}"
            );

            processWithConfirmation(
                '.setActivate',
                "{{ route('admin-managements.activate', ':id') }}",
                "{{ route('admin-managements.show', $data->id) }}",
                'PATCH',
                "Are you sure to activate this admin account's status?",
                "Something went wrong when activating this admin account!",
                "{{ csrf_token() }}"
            );

            processWithConfirmation(
                '.deleteAdmin',
                "{{ route('admin-managements.destroy', ['admin' => ':id']) }}",
                "{{ route('admin-managements.index') }}",
                'DELETE',
                "Are you sure to delete this admin account?",
                "Something went wrong when deleting this admin account!",
                "{{ csrf_token() }}"
            );
        });
    </script>
@endpush
