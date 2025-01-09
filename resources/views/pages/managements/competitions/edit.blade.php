@extends('layouts.master')

@section('title')
    Edit {{ $competition->name }}
@endsection

@section('page-title')
    @yield('title')
@endsection

@section('content')
    <div class="pt-32pt">
        <div class="container">
            <h2 class="mb-2">
                @yield('title')
            </h2>
            <ol class="breadcrumb p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('competition-managements.index') }}">Competitions</a></li>
                <li class="breadcrumb-item"><a
                        href="{{ route('competition-managements.show', $competition->hash) }}">{{ $competition->name }}</a>
                </li>
                <li class="breadcrumb-item active">
                    Edit
                </li>
            </ol>
        </div>
    </div>

    <div class="container page-section">
        <div class="list-group">
            <form action="{{ route('competition-managements.update', ['competition' => $competition]) }}" method="post"
                  enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="list-group-item">
                    <div role="group" aria-labelledby="label-question" class="m-0 form-group">
                        <div class="page-separator">
                            <div class="page-separator__text">Competition Info</div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <x-forms.image-input name="logo" label="Competition Logo" :value="$competition->logo"/>

                                <x-forms.select name="type" label="Competition Type">
                                    <option disabled selected>Select competition type</option>
                                    @foreach(['league', 'Knockout', 'Friendly'] AS $type)
                                        <option value="{{ $type }}" @selected(old('type', $competition->type) == $type)>{{ $type }}</option>
                                    @endforeach
                                </x-forms.select>

                                <x-forms.basic-input type="date" name="startDate" label="Start Date" :value="$competition->startDate"/>
                            </div>
                            <div class="col-lg-6">
                                <x-forms.basic-input type="text" name="name" label="Competition Name" placeholder="Input competition's name ..." :value="$competition->name"/>

                                <x-forms.basic-input type="text" name="location" label="Location" placeholder="Input competition's location ..." :value="$competition->location"/>

                                <x-forms.basic-input type="date" name="endDate" label="End Date" :value="$competition->endDate"/>

                                <x-forms.select name="isInternal" label="Is Internal Competition?">
                                    <option disabled selected>Select yes or no</option>
                                    @foreach(['Yes' => 1, 'No' => 0] AS $label => $value)
                                        <option value="{{ $value }}" @selected(old('isInternal', $competition->isInternal) == $value)>{{ $label }}</option>
                                    @endforeach
                                </x-forms.select>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="list-group-item d-flex justify-content-end">
                    <x-buttons.link-button color="secondary" margin="mx-2" :href="url()->previous()" icon="close" text="Cancel"/>
                    <x-buttons.basic-button type="submit" icon="add" text="Submit"/>
                </div>
            </form>
        </div>
    </div>
@endsection
