<div class="modal fade" id="addCoachModal" tabindex="-1" aria-labelledby="addPlayerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form action='' method="post" id="formAddCoach">
                @method('put')
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Team's Coaches</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group mb-3">
                                <label class="form-label" for="coaches">Coaches</label>
                                <small class="text-danger">*</small>
                                <p>Select the desired coaches to add to the team</p>
                                @if(count($coaches) == 0)
                                    <x-warning-alert text="Currently you haven't create any coach in your academy or there is no coaches left, please create your new coach" :createRoute="route('coach-managements.create')"/>
                                @else
                                    <select class="form-control form-select" id="coaches" name="coaches[]" data-toggle="select" multiple>
                                        <option disabled>Select coaches</option>
                                        @foreach($coaches as $coach)
                                            <option value="{{ $coach->id }}" data-avatar-src="{{ Storage::url($coach->user->foto) }}">
                                                {{ $coach->user->firstName }} {{ $coach->user->lastName }} - {{ $coach->specializations->name }} -
                                                @if(count($coach->teams) == 0)
                                                    No Team
                                                @else
                                                    @foreach($coach->teams as $team)
                                                        {{ $team->teamName }},
                                                    @endforeach
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                                <span class="invalid-feedback coaches_error" role="alert">
                                    <strong></strong>
                                </span>
                            </div>
                        </div>
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

<x-modal-form-update-processing formId="#formAddCoach"
                                updateDataId=""
                                :routeUpdate="$route"
                                modalId="#addCoachModal"/>
@push('addon-script')
    <script>
        $(document).ready(function () {
            $('#add-coaches').on('click', function (e) {
                e.preventDefault();
                $('#addCoachModal').modal('show');
            });
        });
    </script>
@endpush
