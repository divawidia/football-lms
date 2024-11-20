@extends('layouts.master')
@section('title')
    Add {{ $team->teamName }} Coaches
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
                            <li class="breadcrumb-item"><a href="{{ route('team-managements.index') }}">Teams Management</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('team-managements.show', $team->id) }}">{{ $team->teamName }}</a></li>
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
                <form action="{{ route('team-managements.updateCoachTeam', ['team' => $team]) }}" method="post">
                    @method('PUT')
                    @csrf
                    <div class="list-group-item d-flex justify-content-end">
                        <a class="btn btn-secondary mx-2" href="{{ url()->previous() }}"><span class="material-icons mr-2">close</span> Cancel</a>
                        <button type="submit" class="btn btn-primary"><span class="material-icons mr-2">save</span> Save</button>
                    </div>
                    <div class="list-group-item">
                        <div role="group" aria-labelledby="label-question" class="m-0 form-group">
                            <div class="form-group mb-3">
                                <label class="form-label" for="coaches">Coaches</label>
                                <small class="text-danger">*</small>
                                @if(count($coaches) == 0)
                                    <div class="alert alert-light border-1 border-left-4 border-left-accent"
                                         role="alert">
                                        <div class="d-flex flex-wrap align-items-center">
                                            <i class="material-icons mr-8pt">error_outline</i>
                                            <div class="media-body"
                                                 style="min-width: 180px">
                                                <small class="text-black-100">Curently you haven't create any coach in your academy, please create your team</small>
                                            </div>
                                            <div class="ml-8pt mt-2 mt-sm-0">
                                                <a href="{{ route('coach-managements.create') }}"
                                                   class="btn btn-link btn-sm">Create Now</a>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <select class="form-control form-select @error('coaches') is-invalid @enderror" id="coaches" name="coaches[]" data-toggle="select" multiple>
                                        <option disabled>Select coaches to manage this team</option>
                                        @foreach($coaches as $coach)
                                            <option value="{{ $coach->id }}" data-avatar-src="{{ Storage::url($coach->user->foto) }}">
                                                {{ $coach->user->firstName }} {{ $coach->user->lastName }} - {{ $coach->specializations->name }} -
                                                @if(count($coach->teams) == 0)
                                                    No Team
                                                @else
                                                    @foreach($coach->teams as $team)
                                                        <span class="badge badge-pill badge-danger mr-1">{{ $team->teamName }}</span>
                                                    @endforeach
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                                @error('team')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endsection
