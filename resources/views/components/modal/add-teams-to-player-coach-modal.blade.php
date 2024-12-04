<div class="modal fade" id="addTeamModal" tabindex="-1" aria-labelledby="addTeamModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action='' method="post" id="formAddTeam">
                @method('put')
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Update Player's Team</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group mb-3">
                                <label class="form-label" for="teams">Teams</label>
                                <small class="text-danger">*</small>
                                @if(count($teams) == 0)
                                    <div class="alert alert-light border-1 border-left-4 border-left-accent"
                                         role="alert">
                                        <div class="d-flex flex-wrap align-items-center">
                                            <i class="material-icons mr-8pt">error_outline</i>
                                            <div class="media-body"
                                                 style="min-width: 180px">
                                                <small class="text-black-100">Currently there is no team available in your academy, please create your team</small>
                                            </div>
                                            <div class="ml-8pt mt-2 mt-sm-0">
                                                <a href="{{ route('team-managements.create') }}"
                                                   class="btn btn-link btn-sm">Create Now</a>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <select class="form-control form-select add_teams" id="teams" name="teams" data-toggle="select">
                                        <option disabled selected>Select teams</option>
                                        @foreach($teams as $team)
                                            <option value="{{ $team->id }}" data-avatar-src="{{ Storage::url($team->logo) }}">
                                                {{ $team->teamName }} - {{ $team->ageGroup }}
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                                <span class="invalid-feedback teams_error" role="alert">
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

<x-modal-form-update-processing formId="#formAddTeam"
                                updateDataId=""
                                :routeUpdate="$route"
                                modalId="#addTeamModal"/>
@push('addon-script')
    <script>
        $(document).ready(function () {
            $('#add-team').on('click', function (e) {
                e.preventDefault();
                $('#addTeamModal').modal('show');
            });
        });
    </script>
@endpush
