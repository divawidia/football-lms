@extends('includes.admins.master')
@section('title')
    Create Training Videos
@endsection
@section('page-title')
    @yield('title')
@endsection

@section('content')
    <div class="pt-32pt">
        <div class="container page__container d-flex flex-column">
            <h2 class="mb-0">
                @yield('title')
            </h2>
            <ol class="breadcrumb p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('training-videos.create') }}">Training Videos</a></li>
                <li class="breadcrumb-item active">
                    @yield('title')
                </li>
            </ol>
        </div>
    </div>

    <div class="container page__container page-section">
        <div class="list-group">
            <form action="{{ route('training-videos.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="list-group-item">
                    <div role="group" aria-labelledby="label-question" class="m-0 form-group">
                        <div class="page-separator">
                            <div class="page-separator__text">Basic Information</div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-label" for="trainingTitle">Training Title</label>
                                    <small class="text-danger">*</small>
                                    <input type="text"
                                           id="trainingTitle"
                                           name="trainingTitle"
                                           required
                                           value="{{ old('trainingTitle') }}"
                                           class="form-control @error('trainingTitle') is-invalid @enderror"
                                           placeholder="Input training title ...">
                                    @error('trainingTitle')
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Training Preview</label>
                                    <small class="text-danger">*</small>
                                    <div class="media justify-content-center mb-2">
                                        <div class="custom-file">
                                            <input type="file"
                                                   class="custom-file-input @error('previewPhoto') is-invalid @enderror"
                                                   name="previewPhoto"
                                                   id="previewPhoto"
                                                   accept="image/jpg, image/jpeg, image/png"
                                                   required>
                                            <label class="custom-file-label" for="previewPhoto">Choose image</label>
                                            @error('previewPhoto')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                        <img id="preview" class="image-upload-preview img-fluid mt-3"
                                             alt="image-preview" src=""/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-label" for="level">Difficulty Level</label>
                                    <small class="text-danger">*</small>
                                    <select class="form-control form-select @error('level') is-invalid @enderror"
                                            id="level" name="level" required>
                                        <option disabled selected>Select training video's difficulty</option>
                                        @foreach(['Beginner', 'Intermediate', 'Expert'] AS $level)
                                            <option value="{{ $level }}" @selected(old('level') == $level)>{{ $level }}</option>
                                        @endforeach
                                    </select>
                                    @error('level')
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="editor">Description</label>
                                    <small class="text-sm">(Optional)</small>
                                    <div class="editor-container editor-container_classic-editor editor-container_include-style"
                                         id="editor-container">
                                        <div class="editor-container__editor">
                                        <textarea class="form-control h-100 @error('description') is-invalid @enderror"
                                                  id="editor"
                                                  name="description">
                                            {{ old('description') }}</textarea>
                                        </div>
                                    </div>
                                    @error('description')
                                    <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="list-group-item d-flex justify-content-end">
                    <a class="btn btn-secondary mx-2" href="{{ url()->previous() }}"><span class="material-icons mr-2">close</span>
                        Cancel</a>
                    <button type="submit" class="btn btn-primary"><span class="material-icons mr-2">add</span> Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
