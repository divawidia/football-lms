<x-modal.form id="addMatchModal" formId="formAddMatch" title="Create New Team Training Session" :editForm="false" size="">

    <x-forms.basic-input type="text" name="topic" label="Training Session Topic" placeholder="Input training topic, E.g. : Physical conditioning training ..." :modal="true"/>

    <x-forms.basic-input type="text" name="location" label="Training Location" placeholder="Input training location, E.g. : Football field ..." :modal="true"/>

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
            const formId = '#formAddMatch';
            const modalId = '#addMatchModal';

            showModal('.add-training-btn', modalId, formId, function() {
                $.ajax({
                    url: "{{ route('team-managements.all-teams') }}",
                    type: 'GET',
                    success: function(res) {
                        $(modalId).modal('show');

                        $(formId+' #teamId').html('<option disabled selected>Select team for training</option>');
                        $.each(res.data, function (key, value) {
                            {{--$(formId+' #teamId').append('<option value=' + value.id + ' data-avatar-src={{ Storage::url('') }}'+value.logo+'>' + value.teamName + '</option>');--}}
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
            })

            processModalForm(
                formId,
                "{{ route('training-schedules.store') }}",
                null,
                modalId
            );
        });
    </script>
@endpush
