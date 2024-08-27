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
            <img src="{{ \Illuminate\Support\Facades\Storage::url($admin->user->foto) }}"
                 width="104"
                 height="104"
                 class="mr-md-32pt mb-32pt mb-md-0 rounded-circle img-object-fit-cover"
                 alt="instructor">
            <div class="flex mb-32pt mb-md-0">
                <h2 class="text-white mb-0">{{ $fullName  }}</h2>
                <p class="lead text-white-50 d-flex align-items-center">{{ $admin->position }}</p>
            </div>
            <a href="{{ route('admin-managements.edit', $admin->id) }}"
               class="btn btn-outline-white">Edit Profile</a>
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
                            <div class="ml-auto p-2 text-muted">{{ $admin->user->email }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Phone Number :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ $admin->user->phoneNumber }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Last Seen :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ date('l, M d, Y. h:i A', strtotime($admin->user->lastSeen)) }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Date of Birth :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ date('M d, Y', strtotime($admin->user->dob)) }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Gender :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ $admin->user->gender }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Hired Date :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ date('M d, Y', strtotime($admin->hireDate)) }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Created At :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ date('l, M d, Y. h:i A', strtotime($admin->user->created_at)) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 card-group-row__col">
                <div class="card card-sm card-group-row__card">
                    <div class="card-body flex-column">
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Status :</p></div>
                            @if($admin->user->status == '1')
                                <span class="ml-auto p-2 badge badge-pill badge-success">Aktif</span>
                            @elseif($admin->user->status == '0')
                                <span class="ml-auto p-2 badge badge-pill badge-danger">Non Aktif</span>
                            @endif
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Address :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ $admin->user->address }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Country :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ $admin->user->country->name }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">State :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ $admin->user->state->name }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">City :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ $admin->user->city->name }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Zip Code :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ $admin->user->zipCode }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2"><p class="card-title mb-4pt">Last Updated :</p></div>
                            <div class="ml-auto p-2 text-muted">{{ date('l, M d, Y. h:i A', strtotime($admin->user->updated_at)) }}</div>
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
            $('.country-form').on('change', function (){
                const idCountry = this.value;
                $('#state').html('');
                $.ajax({
                    url: "{{url('api/states')}}",
                    data: {
                        fields: 'states',
                        "filters[country_id]": idCountry,
                    },
                    type: 'GET',
                    dataType: 'json',
                    success: function (result){
                        $('#state').html('<option disabled selected>Select State</option>');
                        $.each(result.data, function (key, value){
                            $('#state').append('<option value="'+value.id+'">'+value.name+'</option>');
                        });
                    }
                });
            });
            $('#state').on('change', function (){
                var idState = this.value;
                $('#city').html('');
                $.ajax({
                    url: "{{url('api/cities')}}",
                    data: {
                        fields: 'cities',
                        "filters[state_id]": idState,
                    },
                    dataType: 'json',
                    success: function (result){
                        $('#city').html('<option disabled selected>Select City</option>');
                        $.each(result.data, function (key, value){
                            $('#city').append('<option value="'+value.id+'">'+value.name+'</option>');
                        });
                    }
                });
            });
            foto.onchange = evt => {
                preview = document.getElementById('preview');
                preview.style.display = 'block';
                const [file] = foto.files
                if (file) {
                    preview.src = URL.createObjectURL(file)
                }
            }
        });
    </script>
@endpush
