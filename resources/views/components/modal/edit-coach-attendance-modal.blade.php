<div class="modal fade" id="editCoachAttendanceModal" tabindex="-1" aria-labelledby="editCoachAttendanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="" method="post" id="formEditCoachAttendanceModal">
                @method('PUT')
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="coachName"></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="coachId">
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

<x-modal-form-update-processing formId="#formEditCoachAttendanceModal"
                                updateDataId="#coachId"
                                :routeUpdate="$routeUpdate"
                                modalId="#editCoachAttendanceModal"
                                :routeAfterProcess="$routeAfterProcess"/>

@push('addon-script')
    <script>
        $(document).ready(function (){
            $('.coachAttendance').on('click', function(e) {
                e.preventDefault();
                const id = $(this).attr('id');

                $.ajax({
                    url: "{{ $routeGet }}".replace(':id', id),
                    type: 'get',
                    success: function(res) {
                        $('#editCoachAttendanceModal').modal('show');

                        $('#coachName').text('Update Coach '+res.data.user.firstName+' '+res.data.user.lastName+' Attendance');
                        if (res.data.coachAttendance.attendanceStatus === 'Required Action'){
                            $('#editCoachAttendanceModal #add_attendanceStatus').val('null');
                        } else {
                            $('#editCoachAttendanceModal #add_attendanceStatus').val(res.data.coachAttendance.attendanceStatus);
                        }
                        $('#editCoachAttendanceModal #add_note').val(res.data.coachAttendance.note);
                        $('#coachId').val(res.data.coachAttendance.coachId);
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
        });
    </script>
@endpush
