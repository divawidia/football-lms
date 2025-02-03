<x-modal.form id="editMatchModal" formId="formEditMatch" title="Edit Team Training Session" :editForm="true" size="">
    <x-forms.basic-input type="hidden" name="trainingId" :modal="true"/>

    <x-forms.basic-input type="text" name="topic" label="Training Session Topic" placeholder="Input training topic, E.g. : Physical conditioning training ..." :modal="true"/>

    <x-forms.basic-input type="text" name="location" label="Training Venue" placeholder="Input training venue, E.g. : Football field ..." :modal="true"/>

    <x-forms.select name="teamId" label="Team" :modal="true" :select2="false"></x-forms.select>

    <x-forms.basic-input type="date" name="date" label="Training Date" :modal="true"/>

    <div class="row">
        <div class="col-6">
            <x-forms.basic-input type="time" name="startTime" label="Training Start Time" placeholder="Input training start time ..." :modal="true"/>
        </div>
        <div class="col-6">
            <x-forms.basic-input type="time" name="endTime" label="Training End Time" placeholder="Input training end time ..." :modal="true"/>
        </div>
    </div>

</x-modal.form>

@push('addon-script')
    <script>
        $(document).ready(function (){
            const formId = '#formEditMatch';
            const modalId = '#editMatchModal';

            function getHomeTeams(){
                $.ajax({
                    url: "{{ route('team-managements.all-teams') }}",
                    type: 'GET',
                    success: function(res) {
                        $(formId+' #teamId').html('<option disabled>Select team for training</option>');
                        $.each(res.data, function (key, value) {
                            $(formId+' #teamId').append('<option value=' + value.id + '>' + value.teamName + '</option>');
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
            }

            $('body').on('click', '.edit-training-btn', function(e) {
                e.preventDefault();
                const id = $(this).attr('id');
                $(modalId).modal('show');
                clearModalFormValidation(formId)
                getHomeTeams()

                $.ajax({
                    url: "{{ route('training-schedules.edit', ':id') }}".replace(':id', id),
                    type: 'GET',
                    success: function(res) {
                        $(formId+' #trainingId').val(res.data.id)
                        $.each(res.data, function (key, value) {
                            $(formId+' #'+key).val(value);
                        });
                        $(formId+' #startTime').val(res.data.startTime.substring(0, 5));
                        $(formId+' #endTime').val(res.data.endTime.substring(0, 5));
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        Swal.fire({
                            icon: "error",
                            title: "Something went wrong when retrieving data!",
                            text: errorThrown,
                        });
                    }
                });
            })

            processModalForm(
                formId,
                "{{ route('training-schedules.update', ':id') }}",
                '#trainingId',
                modalId
            );
        });
    </script>
@endpush
