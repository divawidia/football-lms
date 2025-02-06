<x-modal.form id="editCoachAttendanceModal" formId="formEditCoachAttendanceModal" title="" :editForm="true" size="">
    <x-forms.basic-input type="hidden" name="coachId" :modal="true"/>
    <x-forms.select name="attendanceStatus" label="Attendance Status" :modal="true" :select2="false">
        <option value="null" disabled>Select coach's attendance status</option>
        @foreach(['Attended', 'Illness', 'Injured', 'Other'] AS $type)
            <option value="{{ $type }}">{{ $type }}</option>
        @endforeach
    </x-forms.select>
    <x-forms.textarea name="note" label="Attendance Note" placeholder="Input the detailed absent reason (if not attended) ..." :modal="true" :required="false"/>
</x-modal.form>

@push('addon-script')
    <script>
        $(document).ready(function (){
            const formId = '#formEditCoachAttendanceModal';
            const modalId = '#editCoachAttendanceModal';

            $('.coachAttendance').on('click', function(e) {
                e.preventDefault();
                const id = $(this).attr('id');

                @if($training->status != 'Ongoing')
                Swal.fire({
                    icon: "error",
                    title: "You cannot update coach attendance because the training session has not started or has finished or been cancelled!",
                    text: "You can only update attendance while the training session is in ongoing."
                });
                @else
                $.ajax({
                    url: "{{ route('training-schedules.coach', ['training' => $training->hash, 'coach' => ':id']) }}".replace(':id', id),
                    type: 'get',
                    success: function(res) {
                        $(modalId).modal('show');
                        clearModalFormValidation(formId)

                        $(formId+' .modal-title').text('Update Coach '+res.data.user.firstName+' '+res.data.user.lastName+' training Attendance');
                        if (res.data.coachAttendance.attendanceStatus === 'Required Action'){
                            $(formId+' #attendanceStatus').val('null');
                        } else {
                            $(formId+' #attendanceStatus').val(res.data.coachAttendance.attendanceStatus);
                        }
                        $(formId+' #note').val(res.data.coachAttendance.note);
                        $(formId+' #coachId').val(res.data.coachAttendance.coachId);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        Swal.fire({
                            icon: "error",
                            title: "Something went wrong when retrieving Coach attendance data!",
                            text: errorThrown,
                        });
                    }
                });
                @endif
            });

            processModalForm(
                formId,
                "{{ route('training-schedules.update-coach', ['training' => $training->hash, 'coach' => ':id']) }}",
                "#coachId",
                modalId
            );
        });
    </script>
@endpush
