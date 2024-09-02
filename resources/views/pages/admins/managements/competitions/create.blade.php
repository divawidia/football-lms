@extends('layouts.master')
@section('title')
    Create Competition
@endsection
@section('page-title')
    @yield('title')
@endsection

    @section('content')
        <div class="pt-32pt">
            <div class="container page__container d-flex flex-column flex-md-row align-items-center text-center text-sm-left">
                <div class="flex d-flex flex-column flex-sm-row align-items-center mb-24pt mb-md-0">
                    <div class="mb-24pt mb-sm-0 mr-sm-24pt">
                        <h2 class="mb-0">
                            @yield('title')
                        </h2>
                        <ol class="breadcrumb p-0 m-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('competition-managements.index') }}">Competition</a></li>
                            <li class="breadcrumb-item active">
                                @yield('title')
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="container page__container page-section">
            <div class="list-group">
                <form action="{{ route('competition-managements.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="list-group-item d-flex justify-content-end">
                        <a class="btn btn-secondary mx-2" href="{{ route('competition-managements.index') }}"><span class="material-icons mr-2">close</span> Cancel</a>
                        <button type="submit" class="btn btn-primary"><span class="material-icons mr-2">add</span> Submit</button>
                    </div>
                    <div class="list-group-item">
                        <div role="group" aria-labelledby="label-question" class="m-0 form-group">
                            <div class="page-separator">
                                <div class="page-separator__text">Competition Info</div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <label class="form-label">Competition Logo</label>
                                    <small class="text-black-100">(Optional)</small>
                                    <div class="media align-items-center mb-2">
                                            <img src="{{ Storage::url('images/undefined-user.png') }}"
                                                 alt="people"
                                                 width="54"
                                                 height="54"
                                                 id="preview"
                                                 class="mr-16pt rounded-circle img-object-fit-cover" />
                                        <div class="media-body">
                                            <div class="custom-file">
                                                <input type="file"
                                                       class="custom-file-input @error('logo') is-invalid @enderror"
                                                       name="logo"
                                                       id="logo">
                                                <label class="custom-file-label" for="logo">Choose file</label>
                                            </div>
                                        </div>
                                        @error('logo')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="type">Competition Type</label>
                                        <small class="text-danger">*</small>
                                        <select class="form-control form-select @error('type') is-invalid @enderror" id="type" name="type" required data-toggle="select">
                                            <option disabled selected>Select competition type</option>
                                            @foreach(['League', 'Tournament'] AS $type)
                                                <option value="{{ $type }}" @selected(old('ageGroup') == $type)>{{ $type }}</option>
                                            @endforeach
                                        </select>
                                        @error('ageGroup')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="startDate">Start Date</label>
                                        <small class="text-danger">*</small>
                                        <input type="date"
                                               class="form-control @error('startDate') is-invalid @enderror"
                                               id="startDate"
                                               name="startDate"
                                               required
                                               value="{{ old('startDate') }}">
                                        @error('startDate')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="contactName">Contact Name</label>
                                        <small class="text-sm">(Optional)</small>
                                        <input type="text"
                                               class="form-control @error('contactName') is-invalid @enderror"
                                               id="contactName"
                                               name="contactName"
                                               value="{{ old('contactName') }}"
                                            placeholder="Input competition's contact name ...">
                                        @error('contactName')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group mb-4">
                                        <label class="form-label" for="name">Competition Name</label>
                                        <small class="text-danger">*</small>
                                        <input type="text"
                                               id="name"
                                               name="name"
                                               required
                                               value="{{ old('name') }}"
                                               class="form-control @error('name') is-invalid @enderror"
                                               placeholder="Input competition's name ...">
                                        @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="location">Location</label>
                                        <small class="text-danger">*</small>
                                        <input type="text"
                                               id="location"
                                               name="location"
                                               required
                                               value="{{ old('location') }}"
                                               class="form-control @error('location') is-invalid @enderror"
                                               placeholder="Input competition's location ...">
                                        @error('location')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="endDate">End Date</label>
                                        <small class="text-danger">*</small>
                                        <input type="date"
                                               class="form-control @error('endDate') is-invalid @enderror"
                                               id="endDate"
                                               name="endDate"
                                               required
                                               value="{{ old('endDate') }}">
                                        @error('endDate')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="contactPhone">Contact Phone</label>
                                        <small class="text-sm">(Optional)</small>
                                        <input type="text"
                                               class="form-control @error('contactPhone') is-invalid @enderror"
                                               id="contactPhone"
                                               name="contactPhone"
                                               value="{{ old('contactPhone') }}"
                                               placeholder="Input competition's contact name ...">
                                        @error('contactPhone')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="description">Description</label>
                                        <small class="text-sm">(Optional)</small>
                                        <div style="height: 150px"
                                             data-toggle="quill"
                                             data-quill-placeholder="Input competition's description ...">
                                        <textarea
                                            class="form-control @error('description') is-invalid @enderror"
                                            id="description"
                                            name="description">{{ old('description') }}</textarea>
                                        </div>
                                        @error('description')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="page-separator">
                                <div class="page-separator__text">Division</div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endsection
    @push('addon-script')
        <script>
            $(document).ready(function () {
                logo.onchange = evt => {
                    preview = document.getElementById('preview');
                    preview.style.display = 'block';
                    const [file] = logo.files
                    if (file) {
                        preview.src = URL.createObjectURL(file)
                    }
                }
            });
        </script>
    @endpush
