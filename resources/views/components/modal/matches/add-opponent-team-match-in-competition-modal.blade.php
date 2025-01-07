<div class="modal fade" id="addOpponentTeamMatchModal" tabindex="-1" aria-labelledby="addOpponentTeamMatchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="" method="post" id="formAddOpponentTeamMatch">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Opponent Team's Match in Competition {{ $competition->name }}</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="isOpponentTeamMatch" value="1">
                    <div class="form-group">
                        <label class="form-label" for="add_groupId">Group Division</label>
                        <small class="text-danger">*</small>
                        <select class="form-control form-select" id="add_groupId" name="groupId" required data-toggle="select">
                            <option disabled selected>Select group division in this match</option>
                        </select>
                        <span class="invalid-feedback groupId_error" role="alert">
                            <strong></strong>
                        </span>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="add_teamId">Home Teams</label>
                        <small class="text-danger">*</small>
                        <select class="form-control form-select" id="add_teamId" name="teamId" required data-toggle="select">
                        </select>
                        <span class="invalid-feedback teamId_error" role="alert">
                            <strong></strong>
                        </span>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="add_opponentTeamId">Away Teams</label>
                        <small class="text-danger">*</small>
                        <select class="form-control form-select" id="add_opponentTeamId" name="opponentTeamId" required data-toggle="select">
                        </select>
                        <span class="invalid-feedback teamId_error" role="alert">
                            <strong></strong>
                        </span>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="add_date">Match Date</label>
                        <small class="text-danger">*</small>
                        <input type="hidden"
                               class="form-control flatpickr-input"
                               id="add_date"
                               name="date"
                               required
                               value="today"
                               data-toggle="flatpickr">
                        <span class="invalid-feedback date_error" role="alert">
                            <strong></strong>
                        </span>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group mb-3">
                                <label class="form-label" for="add_startTime">Start Time</label>
                                <small class="text-danger">*</small>
                                <input type="text"
                                       id="add_startTime"
                                       name="startTime"
                                       required
                                       class="form-control"
                                       placeholder="Input training's start time ..."
                                       data-toggle="flatpickr"
                                       data-flatpickr-enable-time="true"
                                       data-flatpickr-no-calendar="true"
                                       data-flatpickr-alt-format="H:i"
                                       data-flatpickr-date-format="H:i">
                                <span class="invalid-feedback startTime_error" role="alert">
                                    <strong></strong>
                                </span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mb-3">
                                <label class="form-label" for="add_endTime">End Time</label>
                                <small class="text-danger">*</small>
                                <input type="text"
                                       id="add_endTime"
                                       name="endTime"
                                       required
                                       class="form-control"
                                       placeholder="Input training's end time ..."
                                       data-toggle="flatpickr"
                                       data-flatpickr-enable-time="true"
                                       data-flatpickr-no-calendar="true"
                                       data-flatpickr-alt-format="H:i"
                                       data-flatpickr-date-format="H:i">
                                <span class="invalid-feedback endTime_error" role="alert">
                                    <strong></strong>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label" for="add_place">Match Location</label>
                        <small class="text-danger">*</small>
                        <input type="text"
                               class="form-control"
                               id="add_place"
                               name="place"
                               placeholder="E.g. : Football field ...">
                        <span class="invalid-feedback place_error" role="alert">
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

<x-modal-form-update-processing formId="#formAddOpponentTeamMatch"
                                updateDataId=""
                                :routeUpdate="route('competition-managements.store-match', $competition->id)"
                                modalId="#addOpponentTeamMatchModal"/>
@push('addon-script')
    <script>
        $(document).ready(function (){
            const formId = '#formAddOpponentTeamMatch';

            $('#addOpponentTeamMatch').on('click', function(e) {
                e.preventDefault();

                $.ajax({
                    url: "{{ route('division-managements.get-all', ['competition' => $competition]) }}",
                    type: 'GET',
                    success: function(res) {
                        $('#addOpponentTeamMatchModal').modal('show');

                        $.each(res.data, function (key, value) {
                            $(formId+' #add_groupId').append('<option value=' + value.id + '>' + value.groupName + '</option>');
                        });
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        Swal.fire({
                            icon: "error",
                            title: "Something went wrong when retrieving data!",
                            text: errorThrown,
                        });
                    }
                });
            });

            $(formId+' #add_groupId').on('change', function () {
                const id = $(this).val();

                $.ajax({
                    url: "{{route('division-managements.get-teams', ['competition' => $competition->id ,'group' => ':id']) }}".replace(':id', id),
                    type: 'GET',
                    dataType: 'json',
                    success: function (result) {
                        $(formId+' #add_opponentTeamId').empty();

                        $(formId+' #add_teamId').html('<option disabled selected>Select home team in this match</option>');
                        $.each(result.data.opponentTeams, function (key, value) {
                            $(formId+' #add_teamId').append('<option value=' + value.id + '>' + value.teamName + '</option>');
                        });

                    }
                });
            });

            $(formId+' #add_teamId').on('change', function () {
                const id = $(formId+' #add_groupId').val();
                const exceptTeamId = $(this).val();
                $.ajax({
                    url: "{{route('division-managements.get-teams', ['competition' => $competition->id ,'group' => ':id']) }}".replace(':id', id),
                    type: 'GET',
                    data: {
                        exceptTeamId: exceptTeamId
                    },
                    dataType: 'json',
                    success: function (result) {
                        $(formId+' #add_opponentTeamId').html('<option disabled selected>Select opponent team in this match</option>');
                        $.each(result.data.opponentTeams, function (key, value) {
                            $(formId+' #add_opponentTeamId').append('<option value=' + value.id + '>' + value.teamName + '</option>');
                        });
                    }
                });
            });
        });
    </script>
@endpush
