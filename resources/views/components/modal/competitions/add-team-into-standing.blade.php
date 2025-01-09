<div class="modal fade" id="addTeamModal" tabindex="-1" aria-labelledby="addTeamModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="" method="post" id="formAddTeam">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Team Into Competition Standing</h5>
                    <x-buttons.basic-button :modalCloseIcon="true" :modalDismiss="true"/>
                </div>
                <div class="modal-body">
                    <x-forms.select name="teams" label="Team" :modal="true" :select2="false"></x-forms.select>
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
            const formId = '#formAddTeam';
            const modalId = '#addTeamModal';

            $('#add-team-btn').on('click', function(e) {
                e.preventDefault();

                $.ajax({
                    url: "{{ route('team-managements.all-teams') }}",
                    type: 'GET',
                    success: function(res) {
                        $(modalId).modal('show');

                        $(formId+' #teams').html('<option disabled selected>Select team</option>');
                        $.each(res.data, function (key, value) {
                            {{--$(formId+' #teams').append('<option value=' + value.id + ' data-avatar-src={{ Storage::url('') }}'+value.logo+'>' + value.teamName + '</option>');--}}
                            $(formId+' #teams').append('<option value=' + value.id + '>' + value.teamName + '</option>');
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

            processModalForm(
                formId,
                "{{ route('competition-managements.league-standings.store', $competition->hash) }}",
                null,
                modalId
            );
        });
    </script>
@endpush
