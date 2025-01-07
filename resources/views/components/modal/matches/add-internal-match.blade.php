<div class="modal fade" id="addInternalMatchModal" tabindex="-1" aria-labelledby="addInternalTeamMatchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="" method="post" id="formAddInternalMatch">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Team's Match</h5>
                    <x-buttons.basic-button :modalCloseIcon="true" :modalDismiss="true"/>
                </div>
                <div class="modal-body">
                    <x-forms.select name="teamId" label="Home Team" :modal="true"></x-forms.select>

                    <x-forms.select name="opponentTeamId" label="Away Team" :modal="true"></x-forms.select>

                    <x-forms.basic-input type="date" name="date" label="Match Date" :modal="true"/>

                    <div class="row">
                        <div class="col-6">
                            <x-forms.time-input name="startTime" label="Match Start Time" :modal="true"/>
                        </div>
                        <div class="col-6">
                            <x-forms.time-input name="endTime" label="Match End Time" :modal="true"/>
                        </div>
                    </div>

                    <x-forms.basic-input type="text" name="place" label="Match Venue" placeholder="Input match venue ..." :modal="true"/>

                </div>
                <div class="modal-footer">
                    <x-buttons.basic-button type="button" color="secondary" :modalDismiss="true" text="Cancel"/>
                    <x-buttons.basic-button type="submit" text="Submit"/>
                </div>
            </form>
        </div>
    </div>
</div>

@push('addon-script')
    <script type="module">
        import { processModalForm } from "{{ Vite::asset('resources/js/ajax-processing-data.js') }}" ;
        $(document).ready(function (){
            const formId = '#formAddInternalMatch';

            $('#addInternalMatchBtn').on('click', function(e) {
                e.preventDefault();

                $.ajax({
                    url: "{{ route('team-managements.all-teams') }}",
                    type: 'GET',
                    success: function(res) {
                        $('#addInternalMatchModal').modal('show');

                        $(formId+' #teamId').html('<option disabled selected>Select home team in this match</option>');
                        $.each(res.data, function (key, value) {
                            $(formId+' #teamId').append('<option value=' + value.id + ' data-avatar-src={{ Storage::url('') }}'+value.logo+'>' + value.teamName + '</option>');
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

            $(formId+' #teamId').on('change', function () {
                const id = this.value;

                $.ajax({
                    url: "{{route('team-managements.all-teams') }}",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        exceptTeamId: id
                    },
                    success: function (result) {
                        $(formId+' #opponentTeamId').html('<option disabled selected>Select away team in this match</option>');
                        $.each(result.data, function (key, value) {
                            $(formId+' #opponentTeamId').append('<option value=' + value.id + ' data-avatar-src={{ Storage::url('') }}'+value.logo+'>' + value.teamName + '</option>');
                        });
                    }
                });
            });

            processModalForm(
                '#formAddInternalTeamMatch',
                "{{ route('competition-managements.store-match', $competition->hash) }}",
                null,
                '#addInternalTeamMatchModal'
            );
        });
    </script>
@endpush
