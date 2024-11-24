<div class="modal fade" id="editPlayerAttendanceModal" tabindex="-1" aria-labelledby="editPlayerAttendanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="" method="post" id="formEditPlayerAttendanceModal">
                @method('PUT')
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="playerName"></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="playerId">
                    <div class="form-group">
                        <label class="form-label" for="add_attendanceStatus">Attendance Status</label>
                        <small class="text-danger">*</small>
                        <select class="form-control form-select" id="add_attendanceStatus" name="attendanceStatus" required>
                            <option value="null" disabled>Select player's attendance status</option>
                            @foreach(['Attended', 'Illness', 'Injured', 'Other'] AS $type)
                                <option value="{{ $type }}" @selected(old('attendanceStatus') == $type)>{{ $type }}</option>
                            @endforeach
                        </select>
                        <span class="invalid-feedback attendanceStatus_error" role="alert">
                                <strong></strong>
                            </span>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="add_note">Note</label>
                        <small>(Optional)</small>
                        <textarea class="form-control" id="add_note" name="note" placeholder="Input the detailed absent reason (if not attended)">{{ old('note') }}</textarea>
                        <span class="invalid-feedback note_error" role="alert">
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

@push('addon-script')
    <script>
        $(document).ready(function (){
            $('.playerAttendance').on('click', function(e) {
                e.preventDefault();
                const id = $(this).attr('id');

                $.ajax({
                    {{--url: "{{ route('training-schedules.player', ['schedule' => $data['dataSchedule']->id, 'player' => ":id"]) }}".replace(':id', id),--}}
                    url: "{{ $routeGet }}".replace(':id', id),
                    type: 'get',
                    success: function(res) {
                        $('#editPlayerAttendanceModal').modal('show');

                        $('#playerName').text('Update Player '+res.data.user.firstName+' '+res.data.user.lastName+' Attendance');
                        if (res.data.playerAttendance.attendanceStatus === 'Required Action'){
                            $('#editPlayerAttendanceModal #add_attendanceStatus').val('null');
                        } else {
                            $('#editPlayerAttendanceModal #add_attendanceStatus').val(res.data.playerAttendance.attendanceStatus);
                        }
                        $('#editPlayerAttendanceModal #add_note').val(res.data.playerAttendance.note);
                        $('#playerId').val(res.data.playerAttendance.playerId);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        Swal.fire({
                            icon: "error",
                            title: "Something went wrong when deleting data!",
                            text: errorThrown,
                        });
                    }
                });
            });

            // update player attendance data
            $('#formEditPlayerAttendanceModal').on('submit', function(e) {
                e.preventDefault();
                const id = $('#playerId').val();

                Swal.fire({
                    title: 'Processing...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: "{{ $routeUpdate }}".replace(':id', id),
                    type: $(this).attr('method'),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    success: function(res) {
                        Swal.close();
                        $('#editPlayerAttendanceModal').modal('hide');
                        Swal.fire({
                            title: res.message,
                            icon: 'success',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: "#1ac2a1",
                            confirmButtonText:
                                'Ok!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    },
                    error: function(xhr) {
                        Swal.close();
                        const response = JSON.parse(xhr.responseText);
                        $.each(response.errors, function(key, val) {
                            $('span.' + key + '_error').text(val[0]);
                            $("#add_" + key).addClass('is-invalid');
                        });
                    }
                });
            });

        });
    </script>
@endpush
